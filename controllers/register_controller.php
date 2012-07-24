<?php

Class Register {
    public function welcomeImage(){
        $images = array(
            'usabmx-grand-national-2011-winning.jpg',
            'usabmx-grand-national-2011.jpg',
            'usabmx-hampton-national-1.jpg',
            'usabmx-hampton-national-2.jpg',
            'usabmx-hampton-national-3.jpg',
            'usabmx-tarheel-national-2011-redline.jpg',
            'usabmx-tarheel-national-2011-winning.jpg',
            'usabmx-tarheel-national-2011.jpg'
            );

        return Helpers::makeRandom( $images );
    }
}