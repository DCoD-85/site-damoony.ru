<?PHP

namespace damoony_ru\oxydog;

class algoritm_lang_db_test extends \algoritm_ {
    
    public function default() {
        $vt = new \visuotag();
        $vt->tag('h1')->content('Тест Базы Данных (oxydog)');

        try {
            $this->main->db->open('visuo_damoony');

            $this->main->db->schema()->create('test_items', [
                'id'    => col('id', 'int', length: 11, nullable: false, auto: true),
                'name'  => col('name', 'string', length: 255),
                'price' => col('price', 'int', length: 11)
            ])->run();
            
            $this->main->db->insert('test_items', ['name' => 'Товар 1', 'price' => 100])->affected();
            
            $items = $this->main->db->select("SELECT * FROM test_items")->result();

            // Правильное создание таблицы visuoel
            $ve = new \visuoel();
            $table = $ve->table();
            $table->html_mode(true);
            
            $head = $table->head()->row();
            $head->cell()->set('ID');
            $head->cell()->set('Название');
            $head->cell()->set('Цена');
            
            $body = $table->body();
            foreach ($items as $item) {
                $row = $body->row();
                $row->cell()->set($item['id']);
                $row->cell()->set($item['name']);
                $row->cell()->set($item['price']);
            }
            
            $vt->el($ve); // Добавляем корень visuoel

        } catch (\Throwable $e) {
            $vt->tag('div', class: 'error')->content('❌ Фатальная ошибка: ' . $e->getMessage());
        }

        $this->main->alliasglobal->center->section_add('db_test', $vt);
        return true;
    }
}