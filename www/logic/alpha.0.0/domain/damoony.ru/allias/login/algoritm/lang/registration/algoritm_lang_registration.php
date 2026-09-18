<?PHP
namespace damoony\login;

class algoritm_lang_registration extends \algoritm_ {
	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = false;
	public bool  $default   = true;

	public function guest () {
		# ... форма регистрации ...
		return true;
	}
	public function logged () {
		# ... «вы уже авторизованы» ...
		return true;
	}
}