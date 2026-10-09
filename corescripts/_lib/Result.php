<?PHP

class Result {

	public bool   $ok       = true;
	public array  $steps    = [ ];   // [ ['name'=>..., 'ok'=>bool, 'msg'=>...] ]
	public array  $created  = [ ];   // созданные/изменённые файлы
	public array  $errors   = [ ];
	public array  $warnings = [ ];
	public string $summary  = '';

	public function step ( string $name , bool $ok = true , string $msg = '' ) : void {
		$this->steps[ ] = [ 'name' => $name , 'ok' => $ok , 'msg' => $msg ];
		if ( ! $ok ) $this->ok = false;
	}

	public function file ( string $path ) : void {
		$this->created[ ] = $path;   // абсолютный
	}

	public function error ( string $msg ) : void {
		$this->ok       = false;
		$this->errors[ ] = $msg;
	}

	public function warn ( string $msg ) : void {
		$this->warnings[ ] = $msg;
	}
}