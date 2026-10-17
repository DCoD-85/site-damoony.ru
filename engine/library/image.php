<?PHP

class library_image {
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	# --- resize image and save to new path ---
	# $path             source file path
	# $nPath            destination directory (with trailing separator)
	# $nFilename        new filename without extension
	# $nWidth           target width (0 = no resize)
	# $nType            target format ('' = keep original)
	# $quality          jpeg quality 0-100 (100 = best)
	# $nWidthImportant  true: force resize to nWidth, false: only if source wider
	# $widthToHeight    true: crop to square, false: keep aspect
	# $alpha            true: preserve transparency (png)
	# $rotate           ['maxwidth' => degrees] auto-rotate portrait
	# returns array: [ error, message, filepath, filename ]
	public function image_resize ( string $path ,
	                               string $nPath ,
	                               string $nFilename ,
	                               int $nWidth = 0 ,
	                               string $nType = '' ,
	                               int $quality = 100 ,
	                               bool $nWidthImportant = false ,
	                               bool $widthToHeight = false ,
	                               bool $alpha = false ,
	                               array $rotate = [ ] ) {
		
		# --- check source file ---
		if ( ! is_file( $path ) ) {
			return [ 'error'=>true , 'message'=>'Source file not found: ' . $path , 'filepath'=>'' , 'filename'=>'' ];
		}
		
		# --- get image info ---
		$size = @getimagesize( $path );
		if ( $size === false || empty( $size[ 'mime' ] ) ) {
			return [ 'error'=>true , 'message'=>'Not an image: ' . $path , 'filepath'=>'' , 'filename'=>'' ];
		}
		
		$format = mb_strtolower( substr( $size[ 'mime' ] , strpos( $size[ 'mime' ] , '/' ) + 1 ) );
		
		# --- create source resource ---
		switch ( $format ) {
			case 'jpeg' :
			case 'jpg' :
			case 'jpe' :
				$src = @imagecreatefromjpeg( $path );
				break;
			case 'png' :
				$src = @imagecreatefrompng( $path );
				break;
			case 'gif' :
				$src = @imagecreatefromgif( $path );
				break;
			default :
				return [ 'error'=>true , 'message'=>'Unsupported format: ' . $format , 'filepath'=>'' , 'filename'=>'' ];
		}
		
		if ( $src === false ) {
			return [ 'error'=>true , 'message'=>'Failed to open image' , 'filepath'=>'' , 'filename'=>'' ];
		}
		
		# --- auto-rotate ---
		if ( ! empty( $rotate ) ) {
			$keys = array_keys( $rotate );
			$action = $keys[ 0 ];
			$degrees = $rotate[ $action ];
			
			if ( $action === 'maxwidth' ) {
				$w_src = imagesx( $src );
				$h_src = imagesy( $src );
				if ( $w_src < $h_src ) {
					$rotated = @imagerotate( $src , -$degrees , 0 );
					if ( $rotated !== false ) {
						imagedestroy( $src );
						$src = $rotated;
					}
				}
			}
		}
		
		# --- source dimensions ---
		$w_src = imagesx( $src );
		$h_src = imagesy( $src );
		
		# --- target dimensions ---
		if ( $widthToHeight ) {
			# square: use min side
			$w_src_sq = $w_src;
			$h_src_sq = $h_src;
			if ( $w_src_sq < $h_src_sq ) {
				$h_src_sq = $w_src_sq;
			} else if ( $w_src_sq > $h_src_sq ) {
				$w_src_sq = $h_src_sq;
			}
			
			if ( $nWidth > 0 && $nWidthImportant ) {
				$ratio  = $w_src_sq / $nWidth;
				$w_dest = (int) round( $w_src_sq / $ratio );
				$h_dest = (int) round( $h_src_sq / $ratio );
			} else {
				$w_dest = $w_src_sq;
				$h_dest = $h_src_sq;
			}
		} else {
			if ( $nWidth > 0 && $nWidthImportant ) {
				$ratio  = $w_src / $nWidth;
				$w_dest = (int) round( $w_src / $ratio );
				$h_dest = (int) round( $h_src / $ratio );
			} else {
				$w_dest = $w_src;
				$h_dest = $h_src;
			}
		}
		
		# --- destination resource ---
		$dest = imagecreatetruecolor( $w_dest , $h_dest );
		if ( $dest === false ) {
			imagedestroy( $src );
			return [ 'error'=>true , 'message'=>'Cannot create destination image' , 'filepath'=>'' , 'filename'=>'' ];
		}
		
		# --- transparency ---
		if ( $alpha ) {
			imagealphablending( $dest , false );
			imagesavealpha( $dest , true );
			$transparent = imagecolorallocatealpha( $dest , 0 , 0 , 0 , 127 );
			imagefill( $dest , 0 , 0 , $transparent );
		}
		
		# --- resize ---
		imagecopyresampled( $dest , $src , 0 , 0 , 0 , 0 , $w_dest , $h_dest , $w_src , $h_src );
		
		# --- save ---
		if ( $nType === '' ) $nType = $format;
		$extension = mb_strtolower( ltrim( $nType , '.' ) );
		$nType = '.' . $extension;
		$fullpath = $nPath . $nFilename . $nType;
		
		switch ( $extension ) {
			case 'gif' :
				$bOk = imagegif( $dest , $fullpath );
				break;
			case 'jpg' :
			case 'jpeg' :
			case 'jpe' :
				$bOk = imagejpeg( $dest , $fullpath , $quality );
				break;
			case 'png' :
				# php png quality: 0-9 (0 = no compression, 9 = max)
				# our $quality: 0-100 (100 = best)
				$compression = (int) round( ( 100 - $quality ) * 9 / 100 );
				$bOk = imagepng( $dest , $fullpath , $compression );
				break;
			default :
				$bOk = false;
				break;
		}
		
		imagedestroy( $src );
		imagedestroy( $dest );
		
		return [
			'error'    => ! $bOk ,
			'message'  => $bOk ? '' : 'Failed to save image' ,
			'filepath' => $bOk ? $nPath : '' ,
			'filename' => $bOk ? $nFilename . $nType : ''
		];
	}
}