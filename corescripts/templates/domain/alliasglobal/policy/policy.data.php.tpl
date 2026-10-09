<?php
# =============================================================
#   policy.data.php — данные блока policy
#   Контекст: $this = alliasglobal_policy
#   Доступ: $this->should_show( ), make_shortagreement( ), make_agreement( )
# =============================================================

# короткая информация — всегда
$this->section_add( 'policy_shortagreement' , $this->make_shortagreement( ) );

# popup — если показывать нужно
if ( $this->should_show( ) ) {
	$this->section_add( 'policy_agreement' , $this->make_agreement( ) );
}