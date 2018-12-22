<?php

use Illuminate\Database\Seeder;

class AdminMenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('menu_items')->delete();
        
        \DB::table('menu_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_id' => 1,
                'title' => 'Dashboard',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-speedometer',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 1,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.dashboard',
                'parameters' => 'null',
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 1,
                'title' => 'Media',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-photo-album',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 3,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.media.index',
                'parameters' => 'null',
            ),
            2 => 
            array (
                'id' => 3,
                'menu_id' => 1,
                'title' => 'Users',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-user',
                'color' => '#000000',
                'parent_id' => 15,
                'order' => 2,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.users.index',
                'parameters' => 'null',
            ),
            3 => 
            array (
                'id' => 4,
                'menu_id' => 1,
                'title' => 'Roles',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-door-lock',
                'color' => '#000000',
                'parent_id' => 15,
                'order' => 1,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.roles.index',
                'parameters' => 'null',
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 1,
                'title' => 'Tools',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-wrench-screwdriver-2',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 5,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => NULL,
                'parameters' => '',
            ),
            5 => 
            array (
                'id' => 6,
                'menu_id' => 1,
                'title' => 'Menu Builder',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-hamburger-menu-1',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 2,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.menus.index',
                'parameters' => 'null',
            ),
            6 => 
            array (
                'id' => 7,
                'menu_id' => 1,
                'title' => 'Database',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-database',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 3,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.database.index',
                'parameters' => 'null',
            ),
            7 => 
            array (
                'id' => 8,
                'menu_id' => 1,
                'title' => 'Compass',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-compass-2',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 4,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.compass.index',
                'parameters' => 'null',
            ),
            8 => 
            array (
                'id' => 9,
                'menu_id' => 1,
                'title' => 'BREAD',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-bread-2',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 5,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.bread.index',
                'parameters' => 'null',
            ),
            9 => 
            array (
                'id' => 10,
                'menu_id' => 1,
                'title' => 'Settings',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-settings',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 1,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.settings.index',
                'parameters' => 'null',
            ),
            10 => 
            array (
                'id' => 11,
                'menu_id' => 1,
                'title' => 'Categories',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-grid-squares-2',
                'color' => '#000000',
                'parent_id' => 16,
                'order' => 1,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.categories.index',
                'parameters' => 'null',
            ),
            11 => 
            array (
                'id' => 12,
                'menu_id' => 1,
                'title' => 'Posts',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-article-2',
                'color' => '#000000',
                'parent_id' => 16,
                'order' => 2,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.posts.index',
                'parameters' => 'null',
            ),
            12 => 
            array (
                'id' => 13,
                'menu_id' => 1,
                'title' => 'Pages',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-book-3',
                'color' => '#000000',
                'parent_id' => 16,
                'order' => 3,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.pages.index',
                'parameters' => 'null',
            ),
            13 => 
            array (
                'id' => 14,
                'menu_id' => 1,
                'title' => 'Hooks',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-fishing',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 6,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => 'voyager.hooks',
                'parameters' => 'null',
            ),
            14 => 
            array (
                'id' => 15,
                'menu_id' => 1,
                'title' => 'Users',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-people',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 2,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => NULL,
                'parameters' => '',
            ),
            15 => 
            array (
                'id' => 16,
                'menu_id' => 1,
                'title' => 'CMS',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'icon-internet',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 4,
                'created_at' => '2018-01-01 00:00:00',
                'updated_at' => '2018-01-01 00:00:00',
                'route' => NULL,
                'parameters' => '',
            ),
        ));
        
        
    }
}