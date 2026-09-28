<?PHP

namespace damoony;

abstract class alliasblock {
	# --- refs ---
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;

	# --- state ---
	public \visuotag $visuotag;
	public string $alliasglobal_name = '';
	public string $allias_accepted   = '';
	public bool   $settings_on       = false;

	# --- asmenu ---
	# false → рисуется произвольный HTML блока (scheme/data/release)
	# true  → рисуется результат alliasglobal_menu (все списки)
	public bool $settings_asmenu = false;

	# --- menu ---
	public null|alliasglobal_menu $menu = null;

	# --- override ---
	public null|\visuotag $override_visuotag = null;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal    = $alliasglobal;
		$this->main            = $alliasglobal->main;
		$this->visuorelease    = $this->main->visuorelease;
		$this->visuotag        = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->menu            = new alliasglobal_menu( $alliasglobal , $this->alliasglobal_name );

		$this->init( );
	}

	protected function init ( ) : void {
		$this->algoritm( );
	}

	public function algoritm ( ) : void {
		# override — программист подсунул готовый visuotag
		if ( $this->override_visuotag !== null ) {
			$this->visuotag->clear( )->append( $this->override_visuotag );
			return;
		}

		# data() всегда наполняет меню, независимо от режима
		$this->data( );

		# asmenu === true → только меню
		if ( $this->settings_asmenu === true ) {
			$this->menu_release( );
			return;
		}

		# обычный режим → схема + релиз
		$this->scheme( );
		$this->release( );
	}

	# --- abstract ---
	abstract protected function scheme  ( ) : void;
	abstract protected function data    ( ) : void;
	abstract protected function release ( ) : void;

	# --- menu-only release ---
	# По умолчанию выкладывает все именованные списки меню подряд.
	# Блок может переопределить, если в asmenu-режиме нужно что-то иное.
	protected function menu_release ( ) : void {
		if ( $this->menu === null ) return;

		foreach ( array_keys( $this->menu->lists ) as $name ) {
			$this->visuotag->append( $this->menu->as_tag( $name ) );
		}
	}

	# --- switchers ---

	# Переключить режим отображения и перестроить.
	public function asmenu ( bool $asmenu ) : void {
		$this->settings_asmenu = $asmenu;
		$this->rebuild( );
	}

	# Быстрая перерисовка без пересбора данных:
	# поле меняется → reload() → scheme+release отдадут новое значение.
	public function reload ( ) : void {
		$this->visuotag->clear( );

		if ( $this->override_visuotag !== null ) {
			$this->visuotag->append( $this->override_visuotag );
			return;
		}

		if ( $this->settings_asmenu === true ) {
			$this->menu_release( );
			return;
		}

		$this->scheme( );
		$this->release( );
	}

	# Полная пересборка: сброс меню + override + вызов algoritm().
	public function rebuild ( ) : void {
		$this->menu?->clear( );
		$this->override_visuotag = null;
		$this->visuotag->clear( );
		$this->algoritm( );
	}

	# Полная замена содержимого блока.
	public function override ( \visuotag $vt ) : void {
		$this->override_visuotag = $vt;
		$this->reload( );
	}

	# --- accessors ---
	public function as_tag ( ) : \visuotag {
		return $this->visuotag->root( );
	}

	public function visuotag ( ) : \visuotag {
		return $this->visuotag;
	}
}