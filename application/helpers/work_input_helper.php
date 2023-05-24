<?php 

if (! function_exists('timeUp')) {
    function timeUp($date_time, $per=5){

        // 値がない時、単位が0の時は false を返して終了する
        if( !isset($date_time) || !is_numeric($per) || ($per == 0 )) {
            return false;
        } else {
            $dateObj = new DateTime($date_time);
            // 指定された単位で切り上げる
            // フォーマット文字 i だと、 例えば1分が 2桁の 01 となる(1桁は無い）ので、整数に変換してから切り上げる
            $ceil_num = ceil(sprintf('%d', $dateObj->format('i'))/$per) *$per;
    
            // 切り上げた「分」が60になったら「時間」を1つ繰り上げる
            // 60分 -> 00分に直す
            $hour = $dateObj->format('H');
    
            if ( $ceil_num == 60 ) {
                $hour = $dateObj->modify('+1 hour')->format('H');
                $ceil_num = '00';
            }
            $have = $dateObj->format('Y-m-d ').$hour.':'.sprintf( '%02d', $ceil_num );
    
            return new DateTime($have);
        }
    }
}

if (! function_exists('timeDown')) {
    function timeDown($date_time, $per=5){

        // 値がない時、単位が0の時は false を返して終了する
        if( !isset($date_time) || !is_numeric($per) || ($per == 0 )) {
            return false;
        }else{
            $dateObj = new DateTime($date_time);
    
            // 指定された単位で切り捨てる
            // フォーマット文字 i だと、 例えば1分が 2桁の 01 となる(1桁は無い）ので、整数に変換してから切り捨てる
            $ceil_num = floor(sprintf('%d', $dateObj->format('i'))/$per) *$per;
    
            $hour = $dateObj->format('H');
    
            $have = $dateObj->format('Y-m-d ').$hour.':'.sprintf( '%02d', $ceil_num );
    
            return new DateTime($have);
        }
    }
}