<?PHP
# 
# 
# 

class library_image {
	
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
		# init
		$this->init( );
	}
	
	private function init ( ) {
	} 
	public function about ( ){
		return '<div>This is module ' . __FILE__ . '</div>';
	}
	// .. image
	public function image_resize ( string $path ,
																string $nPath ,
																string $nFilename ,
																int $nWidth = -1 ,	// false
																string $nType = '' ,	// false
																int $quality = 100 ,
																bool $nWidthImportant = false ,
																bool $widthToHeight = false ,
																bool $alpha = false ,
																array $rotate = [ ] ){
		// .. path -
		// .. nPath
		// .. создаём исходное изображение на основе исходного файла и опеределяем его размеры
		
		// .. Определяем исходный формат по MIME-информации, предоставленной функцией getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию.
		if( ! is_file( $path ) ) return '<div>Не указан путь к файлу: ' . $path . '</div>';

		$size = getimagesize( $path );
		$format = mb_strtolower( substr( $size[ 'mime' ] , strpos( $size[ 'mime' ] , '/' ) +1 ) );
		
		// .. если не указан новый тип файла
		if( $nType == '' ) $nType = $format;
		
		
		
		$bOk = true;

		switch( $format ){
			case 'jpeg': case 'jpg': case 'jpe': $src = imagecreatefromjpeg( $path ); break;
			case 'png': $src = imagecreatefrompng( $path ); break;
			case 'gif': $src = imagecreatefromgif( $path ); break;
			default: $bOk = false; break;
		}

		if( is_array( $rotate ) && ! empty( $rotate ) ){
			$keys = array_keys( $rotate );
			$action = $keys[ 0 ];
			$degrees = $rotate[ $action ];
			
			switch( $action ){
				case 'maxwidth' :					
					$w_src = imagesx( $src ); 
					$h_src = imagesy( $src );
					if( $w_src < $h_src ){
						$src = imagerotate( $src , -$degrees , 0 );
					}		
					break;
				default :
					break;
			}
		}
		
		
		
		if( $widthToHeight == true ){
			// Ширина и высота исходной картинки
			$w_src = imagesx( $src ); 
			$h_src = imagesy( $src );
			
			if( $w_src < $h_src ){
				$h_src = $w_src;
			}
			else if( $w_src > $h_src ){
				$w_src = $h_src;
			}
			
			if( $nWidth != false && $nWidthImportant == true ){
				if( $w_src > $nWidth ){
					$ratio = $w_src/$nWidth; 
					$w_dest = round( $w_src/$ratio ); 
					$h_dest = round( $h_src/$ratio ); 
				}
				else if( $w_src < $nWidth ){
					$ratio = $nWidth/$w_src;
					$w_dest = round( $w_src*$ratio );
					$h_dest = round( $h_src*$ratio );
				}
				else {
					$w_dest = $w_src;
					$h_dest = $h_src;
				}
			}
			else{
				$w_dest = $w_src;
				$h_dest = $h_src;
			}
			$pH = 0;
			$pW = 0;
		}
		else {
			// Ширина и высота исходной картинки
			$w_src = imagesx( $src );
			$h_src = imagesy( $src );
			
			// Выводи изображение непосредственно на экран
			// header("Content-type: image/jpeg");
			
			// вычисление пропорций
			// Ширина и высота будущей картинки
			if( $nWidth != false && $nWidthImportant == true ){
				if( $w_src > $nWidth ){
					$ratio = $w_src/$nWidth; 
					$w_dest = round( $w_src/$ratio ); 
					$h_dest = round( $h_src/$ratio ); 
				} else if( $w_src < $nWidth ){
					$ratio = $nWidth/$w_src;
					$w_dest = round( $w_src*$ratio );
					$h_dest = round( $h_src*$ratio );
				} else {
					$w_dest = $w_src;
					$h_dest = $h_src;
				}
			}
			else {
				$w_dest = $w_src;
				$h_dest = $h_src;
			}
			$pH = 0;
			$pW = 0;
		}
		
		// создаём пустую картинку, важно именно truecolor!, иначе будем иметь 8-битный результат 
		$widthToHeight
			? $dest = imagecreatetruecolor( $w_dest , $w_dest )
			: $dest = imagecreatetruecolor( $w_dest , $h_dest );
		
		// .. прозрачная картинка
		if( $alpha == true ){
			$transparent = imagecolorallocatealpha( $dest , 0 , 0 , 0 , 127 );
			imagefill( $dest , 0 , 0 , $transparent );
			imagesavealpha( $dest , true ); // save alphablending setting (important);
		}
		
		$nType = '.' . $nType;

		$spec_redraw = '';
		$spec_redraw == 'simple'
			? imagecopyresized( $dest , $src, $pH , $pW ,  0 , 0 , $w_dest , $h_dest , $w_src , $h_src )
			: imagecopyresampled( $dest , $src , $pW , $pH ,  0 , 0 , $w_dest , $h_dest , $w_src , $h_src );
		
		switch( mb_strtolower( $format ) ){
			case 'gif' : $bOk = imagegif( $dest ,  $nPath.$nFilename.$nType ); break;
			case 'jpg' :
			case 'jpeg' : $bOk = imagejpeg( $dest , $nPath.$nFilename.$nType , $quality ); break;
			case 'png' : $bOk = imagepng( $dest ,  $nPath.$nFilename.$nType , ( $quality / 100 ) - 1 ); break;
			default : $bOk = false; break;
		}
		
		// Освобождаем память
		imagedestroy( $src );
		imagedestroy( $dest );
		
		// Выводи изображение непосредственно на экран
		// header("Content-type: image/png");
		// Изображение создано
		
		
		$returnFilepath = $nPath;
		$returnFilename = $nFilename . $nType;
		
		// .. Отладочная информация ..
		$data = '<div>
			<div>$path: '.$path.'</div>
			<div>$nPath: '.$nPath.'</div>
			<div>$nWidth: '.$nWidth.'</div>
			<div>$quality: '.$quality.'</div>
			<div>$nWidthImportant: '.$nWidthImportant.'</div>
			<div>$widthToHeight: '.$widthToHeight.'</div>
		</div>' ;
		// $_SESSION[ 'messages' ][ ] = [ 'status'=>'console' , 'data'=>$data ];
		
		return [ 'error'=>$error , 'filepath'=>$returnFilepath , 'filename'=>$returnFilename ];
	}
	
}

?>