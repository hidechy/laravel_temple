<?php

namespace App\Http\Controllers\Temple;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

class TempleController extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {

        $photolist = $this->getPhotoUrl();

        $appUrl = "http://" . $_SERVER['HTTP_HOST'] . "/Temple/public";

        //-----------//
        $explanation = [];
        $result = DB::table('t_temple')->orderBy('year')->orderBy('month')->orderBy('day')
            ->get();

        if (isset($result[0])) {
            foreach ($result as $v) {
                $explanation[$v->year . "-" . $v->month . "-" . $v->day]['temple'] = $v->temple;
                $explanation[$v->year . "-" . $v->month . "-" . $v->day]['memo'] = $v->memo;
                $explanation[$v->year . "-" . $v->month . "-" . $v->day]['address'] = $v->address;
                $explanation[$v->year . "-" . $v->month . "-" . $v->day]['station'] = $v->station;
                $explanation[$v->year . "-" . $v->month . "-" . $v->day]['gohonzon'] = $v->gohonzon;
            }
        }
        //-----------//

        return view('temple.index')
            ->with('photolist', $photolist)
            ->with('appUrl', $appUrl)
            ->with('explanation', $explanation);
    }

    /**
     *
     */
    public function callphoto()
    {

        list($date,) = explode("　", $_POST['date']);
        $photolist = $this->getPhotoUrl($date);

        //-----------//
        $rotation = [];
        $rotationfile = "/var/www/html/Temple/public/mySetting/rotation90";
        $content = file_get_contents($rotationfile);
        foreach (explode("\n", $content) as $v) {
            if (trim($v) == "") {
                continue;
            }
            $rotation[] = trim($v);
        }
        //-----------//
        $str = "";

        foreach ($photolist as $imgno => $imgsrc) {
            $str .= "<div class='col-1 p-1'>";
            $str .= "<img src='" . $imgsrc . "' class='img-fluid' onclick='javascript:modalOpen(\"" . $imgsrc . "\");'>";
            $str .= "</div>";
        }

        echo $str;
    }

    /**
     * @param $dir
     * @return array
     */
    private function scandir_r($dir)
    {
        $list = scandir($dir);

        $results = array();

        foreach ($list as $record) {
            if (in_array($record, array(".", ".."))) {
                continue;
            }

            $path = rtrim($dir, "/") . "/" . $record;
            if (is_file($path)) {
                $results[] = $path;
            } else {
                if (is_dir($path)) {
                    $results = array_merge($results, $this->scandir_r($path));
                }
            }
        }

        return $results;
    }

    /**
     * @param null $pickdate
     * @return mixed
     */
    private function getPhotoUrl($pickdate = null)
    {

        //-----------//
        $skiplist = [];
        $skipfile = "/var/www/html/Temple/public/mySetting/skiplist";
        $content = file_get_contents($skipfile);
        foreach (explode("\n", $content) as $v) {
            if (trim($v) == "") {
                continue;
            }
            $skiplist[] = trim($v);
        }
        //-----------//

        //-----------//
        $skiplist2 = [];
        $skipfile = "/var/www/html/Temple/public/mySetting/skiplist2";
        $content = file_get_contents($skipfile);
        foreach (explode("\n", $content) as $v) {
            if (trim($v) == "") {
                continue;
            }
            $skiplist2[] = trim($v);
        }
        //-----------//

        $_dir = "/var/www/html/BrainLog/public/UPPHOTO";
        $filelist = $this->scandir_r($_dir);

        sort($filelist);

        foreach ($filelist as $v) {

            $pos = strpos($v, 'UPPHOTO');
            $str = substr(trim($v), $pos);

            list(, $year, $date, $photo) = explode("/", $str);

            if (in_array($date, $skiplist)) {
                continue;
            }
            if (in_array($photo, $skiplist2)) {
                continue;
            }

            $photolist[$year][$date][] = strtr($v, ['/var/www/html' => 'http://toyohide.work']);
        }

        if (is_null($pickdate)) {
            return $photolist;
        } else {
            list($year, $month, $day) = explode("-", $pickdate);
            return $photolist[$year][$pickdate];
        }
    }

    /**
     * @param null $getdate
     */
    public function output($getdate = null)
    {
        $photoUrl = $this->getPhotoUrl($getdate);
        sort($photoUrl);

        echo json_encode($photoUrl);
    }

    /**
     * @param null $getdate
     */
    public function templephotoapi($getdate = null)
    {
        $photoData = [];
        $photoUrl = $this->getPhotoUrl($getdate);
        sort($photoUrl);
        $photoData['data'] = $photoUrl;
        echo json_encode($photoData);
    }

    /**
     * @param $year
     */
    function templelistapi($year)
    {

        $files = $this->getRandomPhoto($year);

        $explanation = [];

        $result = DB::table('t_temple')
            ->where('year', '=', $year)
            ->orderBy('year')->orderBy('month')->orderBy('day')
            ->get();

        if (isset($result[0])) {

            $i = 0;
            foreach ($result as $v) {
                $str = "";
                $str .= $v->temple;
                if (trim($v->memo) != "") {
                    $str .= "＆" . $v->memo;
                }

                $explanation['data'][$i]['date'] = $v->year . "-" . $v->month . "-" . $v->day;
                $explanation['data'][$i]['temple'] = $str;
                $explanation['data'][$i]['address'] = $v->address;
                $explanation['data'][$i]['station'] = $v->station;
                $explanation['data'][$i]['gohonzon'] = $v->gohonzon;

                $explanation['data'][$i]['photo'] = $files[$v->year . "-" . $v->month . "-" . $v->day];

                $i++;
            }
        }

        echo json_encode($explanation);
    }

    /**
     * @param $year
     */
    private function getRandomPhoto($year)
    {
        //-----------//
        $skiplist = [];
        $skipfile = "/var/www/html/Temple/public/mySetting/skiplist";
        $content = file_get_contents($skipfile);
        foreach (explode("\n", $content) as $v) {
            if (trim($v) == "") {
                continue;
            }
            $skiplist[] = trim($v);
        }
        //-----------//

        //-----------//
        $skiplist2 = [];
        $skipfile = "/var/www/html/Temple/public/mySetting/skiplist2";
        $content = file_get_contents($skipfile);
        foreach (explode("\n", $content) as $v) {
            if (trim($v) == "") {
                continue;
            }
            $skiplist2[] = trim($v);
        }
        //-----------//

        $_dir = "/var/www/html/BrainLog/public/UPPHOTO";
        $filelist = $this->scandir_r($_dir);

        sort($filelist);

        $files2 = [];
        foreach ($filelist as $v) {
            if (!preg_match("/" . $year . "/", trim($v))) {
                continue;
            }

            $files2[] = $v;
        }

        sort($files2);

        $files3 = [];
        foreach ($files2 as $v) {
            $ex_v = explode("/", trim($v));

            $str = [];
            for ($i = 6; $i <= 9; $i++) {
                $str[] = $ex_v[$i];
            }

            if (in_array($ex_v[8], $skiplist)) {
                continue;
            }
            if (in_array($ex_v[9], $skiplist2)) {
                continue;
            }

            $files3[$ex_v[8]][] = "http://toyohide.work/BrainLog/public/" . implode("/", $str);
        }

        foreach ($files3 as $date => $v) {
            $rand = mt_rand(0, count($v) - 1);
            $files[$date] = $v[$rand];
        }

        return $files;
    }

    /**
     * @param null $getdate
     */
    function templelatlngapi($getdate = null)
    {

        list($year, $month, $day) = explode("-", $getdate);

        $explanation = [];

        $result = DB::table('t_temple')
            ->where('year', '=', $year)
            ->where('month', '=', $month)
            ->where('day', '=', $day)
            ->orderBy('year')->orderBy('month')->orderBy('day')
            ->get(['address']);

        if (isset($result[0])) {

            $explanation['data']['lat'] = '';
            $explanation['data']['lng'] = '';

            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $result[0]->address . "&components=country:JP&key=AIzaSyD9PkTM1Pur3YzmO-v4VzS0r8ZZ0jRJTIU";
            $content = file_get_contents($url);
            $jsonStr = json_decode($content);

            if (isset($jsonStr->results[0]->geometry->location->lat) and trim($jsonStr->results[0]->geometry->location->lat) != "") {
                $explanation['data']['lat'] = $jsonStr->results[0]->geometry->location->lat;
            }
            if (isset($jsonStr->results[0]->geometry->location->lng) and trim($jsonStr->results[0]->geometry->location->lng) != "") {
                $explanation['data']['lng'] = $jsonStr->results[0]->geometry->location->lng;
            }
        }

        echo json_encode($explanation);
    }

    /**
     * @param null $getdate
     */
    function templelatlnglistapi($getdate = null)
    {
        $response = [];

        list($year, $month, $day) = explode("-", $getdate);

        $result = DB::table('t_temple')
            ->where('year', '=', $year)
            ->orderBy('year')->orderBy('month')->orderBy('day')
            ->get();

        foreach ($result as $k => $v) {
            $response['data'][$k]['date'] = $v->year . "-" . $v->month . "-" . $v->day;
            $response['data'][$k]['temple'] = $v->temple;
            $response['data'][$k]['address'] = $v->address;
            $response['data'][$k]['station'] = $v->station;
            $response['data'][$k]['gohonzon'] = $v->gohonzon;
            $response['data'][$k]['lat'] = $v->lat;
            $response['data'][$k]['lng'] = $v->lng;
        }

        //----------------------------//
        $jitaku = "千葉県船橋市二子町492-25-101";

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $jitaku . "&components=country:JP&key=AIzaSyD9PkTM1Pur3YzmO-v4VzS0r8ZZ0jRJTIU";
        $content = file_get_contents($url);
        $jsonStr = json_decode($content);

        if (isset($jsonStr->results[0]->geometry->location->lat) and trim($jsonStr->results[0]->geometry->location->lat) != "") {
            $response['home']['lat'] = $jsonStr->results[0]->geometry->location->lat;
        }
        if (isset($jsonStr->results[0]->geometry->location->lng) and trim($jsonStr->results[0]->geometry->location->lng) != "") {
            $response['home']['lng'] = $jsonStr->results[0]->geometry->location->lng;
        }
        //----------------------------//

        echo json_encode($response);
    }

    /**
     * @return mixed
     */
    function templecreate()
    {

        $photoUrl = $this->getPhotoUrl();
        $dirDate = [];
        foreach ($photoUrl as $year => $value) {
            foreach ($value as $date => $value2) {
                $dirDate[] = $date;
            }
        }

        $dbDate = [];
        $result = DB::table('t_temple')->get(['year', 'month', 'day']);
        foreach ($result as $value) {
            $dbDate[] = trim($value->year) . "-" . trim($value->month) . "-" . trim($value->day);
        }

        $diffDate = array_diff($dirDate, $dbDate);

        return view('temple.create')
            ->with('diff_date', $diffDate);
    }

    /**
     * @return mixed
     */
    function templestore()
    {

        if (trim($_POST['date']) != "") {
            list($year, $month, $day) = explode("-", $_POST['date']);

            DB::table('t_temple')
                ->where('year', '=', $year)
                ->where('month', '=', $month)
                ->where('day', '=', $day)
                ->delete();

            $insert = [];
            $insert['year'] = $year;
            $insert['month'] = $month;
            $insert['day'] = $day;


            $_temples = [];

            if (trim($_POST['temple']) != "") {
                $insert['temple'] = trim($_POST['temple']);

                $_temples[] = trim($_POST['temple']);
            }
            if (trim($_POST['memo']) != "") {
                $insert['memo'] = trim($_POST['memo']);

                $ex_memo = explode("、", trim($_POST['memo']));
                foreach ($ex_memo as $e_m) {
                    $_temples[] = $e_m;
                }


            } else {
                $insert['memo'] = '';
            }


            if (trim($_POST['address']) != "") {
                $insert['address'] = trim($_POST['address']);
            }
            if (trim($_POST['station']) != "") {
                $insert['station'] = trim($_POST['station']);
            }
            if (trim($_POST['gohonzon']) != "") {
                $insert['gohonzon'] = trim($_POST['gohonzon']);
            }

            //////////////////////////////
            foreach ($_temples as $_te) {
                $result9 = DB::table('t_temple_latlng')
                    ->where('temple', '=', $_te)
                    ->first();
                if (is_null($result9)) {
                    $insert9 = [];
                    $insert9['temple'] = $_te;
                    DB::table('t_temple_latlng')->insert($insert9);
                }
            }
            //////////////////////////////


            DB::table('t_temple')->insert($insert);
        }

        return redirect('/');
    }


    /**
     *
     */
    function templeaddress()
    {
        $sql = " select * from t_temple_latlng where address is null or address = ''; ";
        $result = DB::select($sql);

        return view('temple.address')
            ->with('result', $result);
    }


    /**
     *
     */
    function templeaddressinput()
    {
        foreach ($_POST as $k => $v) {

            if (trim($v) == "") {
                continue;
            }

            if (preg_match("/address_(.+)/", trim($k), $m)) {

                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $v . "&components=country:JP&key=AIzaSyD9PkTM1Pur3YzmO-v4VzS0r8ZZ0jRJTIU";
                $content = file_get_contents($url);
                $jsonStr = json_decode($content);

                if (isset($jsonStr->results[0]->geometry->location->lat) and trim($jsonStr->results[0]->geometry->location->lat) != "") {
                    $_lat = $jsonStr->results[0]->geometry->location->lat;
                }

                if (isset($jsonStr->results[0]->geometry->location->lng) and trim($jsonStr->results[0]->geometry->location->lng) != "") {
                    $_lng = $jsonStr->results[0]->geometry->location->lng;
                }

                echo $v;
                echo "<br>";
                echo $_lat;
                echo "<br>";
                echo $_lng;
                echo "<br>";
                echo $m[1];
                echo "<hr>";

                $update = [];
                $update['address'] = trim($v);
                $update['lat'] = trim($_lat);
                $update['lng'] = trim($_lng);

                $id = trim($m[1]);

                DB::table('t_temple_latlng')->where('id', '=', $id)->update($update);
            }
        }

        return redirect('/templeaddress');
    }


    /**
     * @param $getdate
     * @return void
     */
    public function templemap($getdate)
    {

        $exDate = explode("-", $getdate);
        $result = DB::table('t_temple')
            ->where('year', $exDate[0])
            ->where('month', $exDate[1])
            ->where('day', $exDate[2])
            ->first();

        $latLng = [
            "{$result->lat}|{$result->lng}"
        ];

        if ($result->memo != "") {
            $exMemo = explode("、", $result->memo);
            foreach ($exMemo as $v) {
                $result2 = DB::table('t_temple_latlng')
                    ->where('temple', trim($v))
                    ->first();

                $latLng[] = "{$result2->lat}|{$result2->lng}";
            }
        }


        return view('temple.map')
            ->with('latLng', $latLng)
            ->with('latLngStr', implode("/", $latLng));

    }


}
