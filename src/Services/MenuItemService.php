<?php


namespace Inensus\OdysseyS3Integration\Services;


class MenuItemService
{
    public function createMenuItems()
    {
        $menuItem = [
            'name' =>'Odyssey S3',
            'url_slug' =>'/odyssey-s3/s3-Overview/page/1',
            'md_icon' =>'grading'
        ];
        $subMenuItems= array();

        return ['menuItem'=>$menuItem,'subMenuItems'=>$subMenuItems];


    }
}
