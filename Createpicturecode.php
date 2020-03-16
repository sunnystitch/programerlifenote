<?php

    /**
     * 生成用户二维码
     * @param
     */
    public static function get_qr_code($tel, int $action)
    {
        #生成二维码底图选择
        if ($action == self::STAFF_CODE_STYLE_1) {
            $code_x = 586;   $code_y = 2930;
            $text1_x = 20;   $text1_y = 2950;
            $text2_x = 20;   $text2_y = 2992;
            $text3_x = 20;   $text3_y = 3032;
            $red = 255; $green = 255; $blue = 255;
            $imagePath = '/library/gentlemanImg/staff1.png';//大语文培优班
        } elseif ($action == self::STAFF_CODE_STYLE_2) {
            $code_x = 586;   $code_y = 3150;
            $text1_x = 20;   $text1_y = 3170;
            $text2_x = 20;   $text2_y = 3212;
            $text3_x = 20;   $text3_y = 3252;
            $red = 0; $green = 0; $blue = 0;
            $imagePath = '/library/gentlemanImg/staff2.png';//大语文拔尖班
        }elseif ($action == self::STAFF_CODE_STYLE_3) {
            $code_x = 586;   $code_y = 2730;
            $text1_x = 20;   $text1_y = 2750;
            $text2_x = 20;   $text2_y = 2792;
            $text3_x = 20;   $text3_y = 2832;
            $red = 0; $green = 0; $blue = 0;
            $imagePath = '/library/gentlemanImg/staff3.png';//大语文拔尖班
        }elseif ($action == self::STAFF_CODE_STYLE_4) {
            $code_x = 586;   $code_y = 2730;
            $text1_x = 25;   $text1_y = 2750;
            $text2_x = 25;   $text2_y = 2792;
            $text3_x = 25;   $text3_y = 2832;
            $red = 0; $green = 0; $blue = 0;
            $imagePath = '/library/gentlemanImg/staff4.png';//大语文拔尖班
        }elseif ($action == self::STAFF_CODE_STYLE_5) {
            $code_x = 586;   $code_y = 3230;
            $text1_x = 20;   $text1_y = 3250;
            $text2_x = 20;   $text2_y = 3292;
            $text3_x = 20;   $text3_y = 3332;
            $red = 0; $green = 0; $blue = 0;
            $imagePath = '/library/gentlemanImg/staff5.png';//大语文拔尖班
        } else {
            return ['check' => false,'data' => '生成二维码底图参数错误'];
        }
        #特殊校区的校验跳转
        if (in_array(trim(self::$city),self::$jiangsusanxiao) ) {
            self::$city_id = 1003;//江苏三校
        } elseif (in_array(trim(self::$city),self::$special_school) ) {
            self::$city_id = 1002;//全国
        } elseif (mb_substr(self::$city,-2,2) == "分校" && !self::$city_id) {
            self::$city_id = 1002;//有分校名称,但是未找到school_id异常的
        }
        //跳转地址
        $path = '/tmp/recruitcard';
        if (self::$staff_source != self::STAFF_SOURCE_SCHOOL) {//机构或加盟校
            if (self::$staff_source == self::STAFF_SOURCE_JOIN) {//加盟校
                $url = "https://bm.zhugexuetang.com?school_id=".self::$city_id."&fromEntrance=2&entrance=2&staff_code=";
                $text = '大语文'.str_replace('分校','',self::$city).self::$campus.'诚挚为您推荐';
            } else {//机构
                $url = "https://bm.zhugexuetang.com?fromEntrance=1&entrance=2&staff_code=";//bm.zhugexuetang.com
                $text = self::$name.'诚挚为您推荐豆神大语文';
            }
        } else {//分校
            if (self::$title == self::STAFF_TITLE_COMMON) {//普通员工
                if (mb_substr(self::$city,-2,2) == "分校") {//存在分校字段
                    $url = "https://bm.zhugexuetang.com?school_id=".self::$city_id."&fromEntrance=2&entrance=2&staff_code=";
                    $text = '我是'.self::$name.'老师，很荣幸为您推荐';
                } else {
                    $url = "https://bm.zhugexuetang.com?fromEntrance=1&entrance=2&staff_code=";
                    $text = '我是'.self::$name.'老师，很荣幸为您推荐';
                }
            } else {//绩效员工
                if (mb_substr(self::$city,-2,2) == "分校") {//存在分校字段
                    $url = "https://bm.zhugexuetang.com?school_id=".self::$city_id."&fromEntrance=2&entrance=2&staff_code=";
                    $text = '我是'.self::$name.'老师，很荣幸为您推荐';
                } else {
                    $url = "https://bm.zhugexuetang.com?fromEntrance=1&entrance=2&staff_code=";
                    $text = '我是'.self::$name.'老师，很荣幸为您推荐';
                }
            }
        }

        $code = self::get_qr_tel($tel);
        $staff_code = self::$staff_source.'_'.self::$city_id.'_'.self::$campus_id.'_'.$code;
        #会写员工表code
        self::updateInfoData(['code'=>$staff_code],['id'=>self::$id]);
        if (!is_dir($path)) {
            @mkdir($path);
        }
        include(ROOT_PATH . '/library/phpqrcode/qrlib.php');
        $qrcodePath = $path . '/' . $tel . '.png';
        \QRcode::png($url.$staff_code, $qrcodePath, 3, 2);

        #制作图片
        #原始图像
        $dst = (ROOT_PATH . $imagePath);
        #得到原始图片信息
        $dst_im1 = imagecreatefrompng($dst);
        $dst_info1 = getimagesize($dst);
        #二维码图片
        $src1 = $qrcodePath;
        $src1_im = imagecreatefrompng($src1);
        $src1_info = getimagesize($src1);
        #合并水印图片
        imagecopymerge($dst_im1,$src1_im,$code_x,$code_y,0,0,$src1_info[0],$src1_info[1],100);
        #合成图片的路径
        $path = '/tmp/gentlemancard';
        if (!is_dir($path)) {
            @mkdir($path);
        }
        $poster_marge_path =$path . '/' . date('Y-m-d').'-'.$tel . '.png';
        #输出合并后水印图片
        ob_start();
        imagepng($dst_im1,$poster_marge_path);
        imagedestroy($dst_im1);
        imagedestroy($src1_im);

        #添加图片文字
        $dst_path = $poster_marge_path;
        $dst_new = imagecreatefromstring(file_get_contents($dst_path));
        $font = (ROOT_PATH . '/library/gentlemanImg/fzlthk.ttf');
        $im = imagecreatetruecolor(400, 30);
        $colour =imagecolorallocate($im, $red,$green,$blue);
        #判断字数
        $size = 20;
        if (mb_strlen($text) > 16) {
            $size = 15;
        }
        imagefttext($dst_new, $size, 0,$text1_x, $text1_y, $colour, $font, $text);//城市
        imagefttext($dst_new, $size, 0,$text2_x, $text2_y, $colour, $font, '扫描右方二维码会看到');
        imagefttext($dst_new, $size, 0,$text3_x, $text3_y, $colour, $font, '最适合您孩子的1~4个班级');
        header('Content-type: image/png');
        imagepng($dst_new);
        @unlink($dst_new);
        $sImage = ob_get_contents();
        ob_end_clean();
        $base64_img = base64_encode($sImage);
        @unlink($qrcodePath);

        return ['check' => true,'data' => $base64_img];

    }
