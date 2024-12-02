<?php


namespace App\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

final class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $child = $menu->getChild('Vehicles')->addChild('maps', [
            'label' => 'Mapview',
            'route' => 'app_map_view',
        ])->setExtras([
            'icon' => 'fa fa-map-marker', // html is also supported
        ]);
        $menu->getChild('Transactions')->addChild('transactions_chart',[
            'label' => 'Tranasction Chart',
            'route' => 'app_admin_chart',
        ])->setExtras([
            'icon' => 'fa fa-bar-chart', // html is also supported
        ]);
        /*$menu->getChild('Transactions')->addChild('versement_chart',[
            'label' => 'Versement Chart',
            'route' => 'app_admin_chart_versement',
        ])->setExtras([
            'icon' => 'fa fa-bar-chart', // html is also supported
        ]);*/
    }
}