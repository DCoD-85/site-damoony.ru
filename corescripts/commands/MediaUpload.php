<?PHP

class MediaUpload {

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result();
        $dir  = (string)( $params['dir'] ?? '' );
        $file = $params['file'] ?? null;

        if ( empty($dir) ) { $r->error('Папка назначения не указана'); return $r; }
        if ( !$file || $file['error'] !== UPLOAD_ERR_OK ) { $r->error('Ошибка загрузки файла'); return $r; }

        $ext = strtolower( pathinfo( $file['name'] , PATHINFO_EXTENSION ) );
        $allowed = [ 'jpg' , 'jpeg' , 'png' , 'gif' , 'webp' , 'svg' , 'pdf' , 'zip' , 'css' , 'js' , 'ico' , 'woff2' , 'woff' , 'ttf' , 'mp4' , 'mp3' , 'doc' , 'docx' , 'xls' , 'xlsx' ];
        
        if ( ! in_array( $ext , $allowed , true ) ) {
            $r->error('Недопустимое расширение файла. Разрешены: ' . implode(', ', $allowed));
            return $r;
        }

        // Гарантируем, что папка существует
        Fs::mkdir( $dir );

        // Очистка имени файла от опасных символов
        $name = preg_replace( '/[^a-zA-Z0-9_\-\.]/' , '_' , basename( $file['name'] ) );
        $dest = $dir . '/' . $name;
        
        // Защита от перезаписи существующих файлов
        $i = 1;
        while ( is_file( $dest ) ) {
            $dest = $dir . '/' . pathinfo( $name , PATHINFO_FILENAME ) . '_' . $i . '.' . $ext;
            $i++;
        }

        // Умная обработка изображений (WebP + ресайз)
        if ( in_array( $ext , [ 'jpg' , 'jpeg' , 'png' , 'gif' , 'webp' ] , true ) ) {
            $info = @getimagesize( $file['tmp_name'] );
            if ( $info !== false ) {
                $image = null;
                if ( $ext === 'jpg' || $ext === 'jpeg' ) $image = @imagecreatefromjpeg( $file['tmp_name'] );
                elseif ( $ext === 'png' ) $image = @imagecreatefrompng( $file['tmp_name'] );
                elseif ( $ext === 'gif' ) $image = @imagecreatefromgif( $file['tmp_name'] );
                elseif ( $ext === 'webp' ) $image = @imagecreatefromwebp( $file['tmp_name'] );

                if ( $image ) {
                    $width = imagesx( $image );
                    $height = imagesy( $image );
                    
                    // Сжимаем большие картинки (макс. ширина 1920px)
                    if ( $width > 1920 ) {
                        $newHeight = floor( $height * ( 1920 / $width ) );
                        $resized = imagescale( $image , 1920 , $newHeight );
                        imagedestroy( $image );
                        $image = $resized;
                    }
                    
                    // Принудительно меняем расширение на .webp
                    $dest = pathinfo( $dest , PATHINFO_DIRNAME ) . '/' . pathinfo( $dest , PATHINFO_FILENAME ) . '.webp';
                    
                    if ( function_exists( 'imagewebp' ) ) {
                        imagewebp( $image , $dest , 80 ); // Качество 80%
                    } else {
                        move_uploaded_file( $file['tmp_name'] , $dest ); // Fallback
                    }
                    imagedestroy( $image );
                } else {
                    move_uploaded_file( $file['tmp_name'] , $dest );
                }
            } else {
                move_uploaded_file( $file['tmp_name'] , $dest );
            }
        } else {
            // Обычные файлы (PDF, ZIP, CSS, JS, MP4 и т.д.)
            move_uploaded_file( $file['tmp_name'] , $dest );
        }

        $r->step( 'Файл загружен' , true , basename( $dest ) );
        return $r;
    }
}