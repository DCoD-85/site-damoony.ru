<?PHP

# ============================================================
#   Suite: visuoel_field_wrap — обёртки полей (label + input + hint)
# ============================================================

return [

    'title' => 'visuoel_field_wrap',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        # class_exists
        'field_text class' => function ( ) { Assert::true( class_exists( 'visuoel_field_text' ) ); },
        'field_password class' => function ( ) { Assert::true( class_exists( 'visuoel_field_password' ) ); },
        'field_email class' => function ( ) { Assert::true( class_exists( 'visuoel_field_email' ) ); },
        'field_file class' => function ( ) { Assert::true( class_exists( 'visuoel_field_file' ) ); },
        'field_textarea class' => function ( ) { Assert::true( class_exists( 'visuoel_field_textarea' ) ); },
        'field_select class' => function ( ) { Assert::true( class_exists( 'visuoel_field_select' ) ); },
        'field_checkbox class' => function ( ) { Assert::true( class_exists( 'visuoel_field_checkbox' ) ); },
        'field_radio class' => function ( ) { Assert::true( class_exists( 'visuoel_field_radio' ) ); },
        'field_checkbox_group class' => function ( ) { Assert::true( class_exists( 'visuoel_field_checkbox_group' ) ); },
        'field_radio_group class' => function ( ) { Assert::true( class_exists( 'visuoel_field_radio_group' ) ); },

        # field_text
        'field_text: label + hint' => function ( ) {
            $ve = new visuoel( );
            $f = $ve->field( )->text( );
            $f->data( )->name = 'login';
            $f->data( )->label( 'Логин' )->hint( 'Латиница, 3-32 символа' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_text">' , $h );
            Assert::contains( '<div class="box">' , $h );
            Assert::contains( '<label class="field-label" for="field_login">' , $h );
            Assert::contains( '>Логин</label>' , $h );
            Assert::contains( '<div class="border-box">' , $h );
            Assert::contains( '<input type="text"' , $h );
            Assert::contains( 'id="field_login"' , $h );
            Assert::contains( 'name="login"' , $h );
            Assert::contains( '<div class="hint">Латиница, 3-32 символа</div>' , $h );
        },

        'field_text: hide_label + hide_hint' => function ( ) {
            $ve = new visuoel( );
            $f = $ve->field( )->text( );
            $f->data( )->name = 'x';
            $f->data( )->label( 'Скрытый' )->hint( 'Скрытый hint' );
            $f->data( )->hide_label( )->hide_hint( );
            $ve->init( );
            $h = $ve->release( );
            Assert::notContains( 'Скрытый' , $h );
            Assert::notContains( 'Скрытый hint' , $h );
        },

        # field_checkbox
        'field_checkbox: базовая разметка' => function ( ) {
            $ve = new visuoel( );
            $c = $ve->field( )->checkbox( );
            $c->data( )->name = 'remember';
            $c->data( )->label( 'Запомнить меня' );
            $c->data( )->checked = true;
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_checkbox">' , $h );
            Assert::contains( '<label class="field-label">' , $h );
            Assert::contains( '<span class="border-box">' , $h );
            Assert::contains( 'type="checkbox"' , $h );
            Assert::contains( 'id="field_remember"' , $h );
            Assert::contains( 'name="remember"' , $h );
            Assert::contains( 'checked="checked"' , $h );
            Assert::contains( '<span class="field-name">Запомнить меня</span>' , $h );
        },

        # field_radio
        'field_radio: базовая разметка' => function ( ) {
            $ve = new visuoel( );
            $r = $ve->field( )->radio( );
            $r->data( )->name  = 'gender';
            $r->data( )->value = 'm';
            $r->data( )->label( 'Мужской' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_radio">' , $h );
            Assert::contains( 'type="radio"' , $h );
            Assert::contains( 'id="field_gender_m"' , $h );
            Assert::contains( 'name="gender"' , $h );
            Assert::contains( 'value="m"' , $h );
            Assert::contains( '<span class="field-name">Мужской</span>' , $h );
        },

        # field_textarea
        'field_textarea: базовая разметка' => function ( ) {
            $ve = new visuoel( );
            $t = $ve->field( )->textarea( );
            $t->data( )->name = 'bio';
            $t->data( )->label( 'О себе' )->hint( 'Пара слов' );
            $t->data( )->rows = 4;
            $t->data( )->cols = 40;
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_textarea">' , $h );
            Assert::contains( '<label class="field-label" for="field_bio">' , $h );
            Assert::contains( '<textarea' , $h );
            Assert::contains( 'id="field_bio"' , $h );
            Assert::contains( 'name="bio"' , $h );
            Assert::contains( 'rows="4"' , $h );
            Assert::contains( 'cols="40"' , $h );
            Assert::contains( '</textarea>' , $h );
        },

        # field_select
        'field_select: базовая разметка' => function ( ) {
            $ve = new visuoel( );
            $s = $ve->field( )->select( );
            $s->data( )->name = 'city';
            $s->data( )->label( 'Город' );
            $s->data( )->options = [ '' => '— выберите —' , 'msk' => 'Москва' , 'spb' => 'Санкт-Петербург' ];
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_select">' , $h );
            Assert::contains( '<label class="field-label" for="field_city">' , $h );
            Assert::contains( '<select' , $h );
            Assert::contains( 'id="field_city"' , $h );
            Assert::contains( 'name="city"' , $h );
            Assert::contains( '<option value="msk">Москва</option>' , $h );
            Assert::contains( '<option value="spb">Санкт-Петербург</option>' , $h );
            Assert::contains( '</select>' , $h );
        },

        # password / email / file
        'field_password + email + file' => function ( ) {
            $ve = new visuoel( );

            $p = $ve->field( )->password( );
            $p->data( )->name = 'pass';
            $p->data( )->label( 'Пароль' );

            $e = $ve->field( )->email( );
            $e->data( )->name = 'mail';
            $e->data( )->label( 'Email' );

            $f = $ve->field( )->file( );
            $f->data( )->name = 'avatar';
            $f->data( )->label( 'Аватар' );
            $f->data( )->accept = 'image/*';

            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_password">' , $h );
            Assert::contains( 'type="password"' , $h );
            Assert::contains( '<div class="field field_email">' , $h );
            Assert::contains( 'type="email"' , $h );
            Assert::contains( '<div class="field field_file">' , $h );
            Assert::contains( 'type="file"' , $h );
            Assert::contains( 'accept="image/*"' , $h );
        },

        # checkbox_group
        'field_checkbox_group: сборка' => function ( ) {
            $ve = new visuoel( );
            $g = $ve->field( )->checkbox_group( );
            $g->data( )->label( 'Интересы' )->hint( 'Можно несколько' );

            $c1 = $ve->field( )->checkbox( );
            $c1->data( )->name = 'interests[]';
            $c1->data( )->label( 'Спорт' )->value( 'sport' );
            $g->add( $c1 );

            $c2 = $ve->field( )->checkbox( );
            $c2->data( )->name = 'interests[]';
            $c2->data( )->label( 'Музыка' )->value( 'music' )->checked( true );
            $g->add( $c2 );

            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_checkbox_group">' , $h );
            Assert::contains( '<div class="field-label">Интересы</div>' , $h );
            Assert::contains( '<div class="options">' , $h );
            Assert::contains( 'id="field_interests_sport"' , $h );
            Assert::contains( 'id="field_interests_music"' , $h );
            Assert::contains( '<span class="field-name">Спорт</span>' , $h );
            Assert::contains( '<span class="field-name">Музыка</span>' , $h );
            Assert::contains( 'checked="checked"' , $h );
            Assert::contains( '<div class="hint">Можно несколько</div>' , $h );
        },

        # radio_group
        'field_radio_group: сборка' => function ( ) {
            $ve = new visuoel( );
            $g = $ve->field( )->radio_group( );
            $g->data( )->label( 'Пол' );

            $r1 = $ve->field( )->radio( );
            $r1->data( )->name = 'gender';
            $r1->data( )->label( 'Мужской' )->value( 'm' );
            $g->add( $r1 );

            $r2 = $ve->field( )->radio( );
            $r2->data( )->name = 'gender';
            $r2->data( )->label( 'Женский' )->value( 'f' )->checked( true );
            $g->add( $r2 );

            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_radio_group">' , $h );
            Assert::contains( '<div class="field-label">Пол</div>' , $h );
            Assert::contains( 'id="field_gender_m"' , $h );
            Assert::contains( 'id="field_gender_f"' , $h );
            Assert::contains( '<span class="field-name">Мужской</span>' , $h );
            Assert::contains( '<span class="field-name">Женский</span>' , $h );
            Assert::contains( 'checked="checked"' , $h );
        },

        # field_block
        'field_block: text' => function ( ) {
            $ve = new visuoel( );
            $b = new visuoel_field_block( );
            $b->data( )->text( 'Просто текст описания' );
            $ve->add( $b );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="field field_block">' , $h );
            Assert::contains( '<div class="box">' , $h );
            Assert::contains( '<div class="field-content">Просто текст описания</div>' , $h );
        },

        'field_block: html raw' => function ( ) {
            $ve = new visuoel( );
            $b = new visuoel_field_block( );
            $b->data( )->html( '<b>жирный</b>' );
            $ve->add( $b );
            $ve->init( );
            Assert::contains( '<b>жирный</b>' , $ve->release( ) );
        },

        # XSS
        'XSS label — экранирован' => function ( ) {
            $ve = new visuoel( );
            $f = $ve->field( )->text( );
            $f->data( )->name = 'x';
            $f->data( )->label( '<script>alert(1)</script>' );
            $ve->init( );
            $h = $ve->release( );
            Assert::notContains( '<script>' , $h );
            Assert::contains( '&lt;script&gt;' , $h );
        },

        'XSS hint — экранирован' => function ( ) {
            $ve = new visuoel( );
            $f = $ve->field( )->text( );
            $f->data( )->name = 'x';
            $f->data( )->hint( '"><script>alert(1)</script>' );
            $ve->init( );
            Assert::notContains( '<script>' , $ve->release( ) );
        },

        'XSS error — экранирован' => function ( ) {
            $ve = new visuoel( );
            $f = $ve->field( )->text( );
            $f->data( )->name = 'x';
            $f->data( )->error( '<script>x()</script>' );
            $ve->init( );
            Assert::notContains( '<script>' , $ve->release( ) );
        },

    ],
];