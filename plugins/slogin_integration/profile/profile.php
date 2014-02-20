<?php
/**
 * Social Login Integration Plugin Profile
 *
 * @version 	1.0
 * @author		Arkadiy, Joomline
 * @copyright	© 2013. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.image.image');

require_once JPATH_ROOT.'/plugins/slogin_integration/profile/lib/profiles.php';
require_once JPATH_ROOT.'/plugins/slogin_integration/profile/lib/geo.php';
require_once JPATH_BASE.'/components/com_slogin/controller.php';

class plgSlogin_integrationProfile extends JPlugin
{
    public function onAfterSloginStoreUser($user, $provider, $info)
    {
        $this->createProfile($user, $provider, $info);
    }

    public function onAfterSloginLoginUser($user, $provider, $info)
    {
        if(!method_exists($this, $provider."GetData")) return;

        if($this->issetProfile($user, $provider)){
            $data = call_user_func_array(array($this, $provider."GetData"), array($user, $provider, $info));
            $this->updateAvatar($user, $provider, $data->picture, $info);
            $this->updateCurrentProfile($user, $provider);
        }
        else{
            $this->createProfile($user, $provider, $info);
        }
    }

    public function onAfterSloginDeleteSloginUser($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__slogin_users');
        $query->where($db->quoteName('id') . ' = ' . $db->quote($id));
        $db->setQuery((string)$query, 0, 1);
        $res = $db->loadObject();
        $this->deleteProfile($res);
    }

    public function onAfterSloginDeleteUser($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__slogin_users');
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userId));
        $db->setQuery((string)$query);
        $res = $db->loadObjectList();
        if(count($res)>0){
            foreach($res as $v){
                $this->deleteProfile($v);
            }
        }
    }

    private function deleteProfile($row)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $rootfolder = $this->params->get('rootfolder', 'images/avatar');
        $file = JPATH_ROOT . '/' . $rootfolder . '/' . $row->photo_src;
        if (is_file($file))
        {
            JFile::delete($file);
        }
        $query->delete();
        $query->from($db->quoteName('#__plg_slogin_profile'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($row->user_id));
        $query->where($db->quoteName('provider') . ' = ' . $db->quote($row->provider));
        $db->setQuery($query);
        $db->query();
    }

    private function googleGetData($user, $provider, $info){
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        $data->social_profile_link = $info->link;
        $data->f_name = $info->given_name;
        $data->l_name = $info->family_name ;
        $data->email = $info->email;
        if($info->gender == 'male')
            $data->gender = 1;
        elseif($info->gender == 'female')
            $data->gender = 2;
        else
            $data->gender = 0;
        $this->getGeoInfo($data);
        $data->picture = isset($info->picture) ? $info->picture : '';
        return $data;
    }

    private function uloginGetData($user, $provider, $info){
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = 'ulogin_' . $info->network . '_' . $info->uid;
        $data->provider = $provider;
        $data->social_profile_link = isset($info->profile) ? $info->profile : '';
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name;
        $data->email = isset($info->email) ? $info->email: '';
        $data->gender = (int)$info->sex;
        $this->getGeoInfo($data);
        $data->picture = isset($info->photo) ? $info->photo : '';
        return $data;
    }

    private function linkedinGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        $data->social_profile_link = 'http://www.linkedin.com/profile/view?id='.$info->id ;
        $data->gender = 0;
        $data->f_name = $info->firstName;
        $data->l_name = $info->lastName;
        $this->getGeoInfo($data);
        $data->picture = isset($info->pictureUrl) ? $info->pictureUrl : '';
        return $data;
    }

    private function vkontakteGetData($user, $provider, $info)
    {
        $controller = new SLoginController();
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->uid;
        $data->provider = $provider;
        $data->gender = 0;
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name ;
        $data->phone = $info->home_phone;
        $data->mobil_phone = isset($info->mobile_phone) ? $info->mobile_phone : '';
        $data->social_profile_link = 'http://vk.com/id'.$info->uid;
        $this->getGeoInfo($data);
        $ResponseUrl = 'https://api.vk.com/method/getProfiles?uid=' . $info->uid . '&fields=photo_medium';
        $request = json_decode($controller->open_http($ResponseUrl))->response[0];
        $data->picture = (empty($request->error) && substr($request->photo_medium, -12, 10000) != 'camera_b.gif') ? $request->photo_medium : '';
        return $data;
    }

    private function facebookGetData($user, $provider, $info)
    {
        $controller = new SLoginController();
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        $data->social_profile_link = $info->link;
        if($info->gender == 'male')
            $data->gender = 1;
        elseif($info->gender == 'female')
            $data->gender = 2;
        else
            $data->gender = 0;
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name;
        $data->email = $info->email;
        $this->getGeoInfo($data);
        $foto_url = 'http://graph.facebook.com/' . $info->id . '/picture?type=square&redirect=false';
        $request_foto = json_decode($controller->open_http($foto_url));
        $data->picture = '';
        if (empty($request_foto->error)){
            if ($request_foto->data->is_silhouette === false) { //если аватар загружен
                if ($request_foto->data->url) {
                    $data->picture = $request_foto->data->url;
                }
            }
        }
        return $data;
    }

    private function twitterGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        $data->social_profile_link = 'https://twitter.com/'.$info->screen_name;
        $data->gender = 0;
        $data->f_name = $info->name;
        $this->getGeoInfo($data);
        $data->picture = ($info->default_profile_image != 1) ? $info->profile_image_url : '';
        return $data;
    }

    private function odnoklassnikiGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->uid;
        $data->provider = $provider;
        $data->social_profile_link = 'http://www.odnoklassniki.ru/profile/'.$info->uid;
        if($info->gender == 'male')
            $data->gender = 1;
        elseif($info->gender == 'female')
            $data->gender = 2;
        else
            $data->gender = 0;
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name;
        $data->email = $info->email;
        $this->getGeoInfo($data);
        $data->picture = (substr($info->pic_1, -14) != 'stub_50x50.gif') ? $info->pic_2 : '';
//        $data->picture = '';
        return $data;
    }
    private function mailGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->uid;
        $data->provider = $provider;
        $data->social_profile_link = $info->link;
        if($info->sex == 0)
            $data->gender = 1;
        elseif($info->sex == 1)
            $data->gender = 2;
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name;
        $data->email = $info->email;
        $this->getGeoInfo($data);
        $data->picture = ($info->has_pic == '1') ? $info->pic_50 : '';
        return $data;
    }
    private function yandexGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        //$data->social_profile_link = $info->link;

        if($info->sex == 'male')
            $data->gender = 1;
        else
            $data->gender = 2;
        $data->f_name = $info->real_name;
        $data->email = $info->default_email;
        $this->getGeoInfo($data);
        $data->picture = '';
        return $data;
    }

    private function liveGetData($user, $provider, $info)
    {
        $data = new StdClass();
        $data->user_id = $user->id;
        $data->slogin_id = $info->id;
        $data->provider = $provider;
        //$data->social_profile_link = $info->link;
        $data->gender = 0;
        $data->f_name = $info->first_name;
        $data->l_name = $info->last_name;
        if(!empty($info->emails->preferred))
            $data->email = $info->emails->preferred;
        else if(!empty($info->emails->account))
            $data->email = $info->emails->account;
        else if(!empty($info->emails->personal))
            $data->email = $info->emails->personal;
        else if(!empty($info->emails->business))
            $data->email = $info->emails->business;
        $this->getGeoInfo($data);
        $data->picture = '';
        return $data;
    }

    private function getGeoInfo(&$data){
        if($this->params->get('enable_geo', 0))
        {
            $geo = new SloginGeo(array('charset'=>'UTF-8', 'ip'=>$_SERVER["REMOTE_ADDR"]));
            $geoData = $geo->get_geobase_data();
            $data->country = (!empty($geoData["country"])) ? $geoData["country"] : '';
            $data->region = (!empty($geoData["region"])) ? $geoData["region"] : '';
            $data->city = (!empty($geoData["city"])) ? $geoData["city"] : '';
            $data->lat = (!empty($geoData["lat"])) ? $geoData["lat"] : '';
            $data->lng = (!empty($geoData["lng"])) ? $geoData["lng"] : '';
        }
    }

    private function createProfile($user, $provider, $info)
    {
        if (!$provider) return;
        if(!method_exists($this, $provider."GetData")) return;

        //максимальная ширина и высота для генерации изображения
        $max_h = $this->params->get('imgparam', 50);
        $max_w = $this->params->get('imgparam', 50);

        $data = call_user_func_array(array($this, $provider."GetData"), array($user, $provider, $info));
        if (isset($data->picture)){
            $origimage = $info->picture;
            $id = isset($info->id) ? $info->id : $info->uid;
            $new_image = $provider . '_' . $id . '.jpg';
            if ($this->createAvatar($origimage, $new_image, $max_w, $max_h)) {
                $data->avatar = $new_image;
            }
        }

        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__plg_slogin_profile');
        $q->set('`current_profile` = 0');
        $q->where('`user_id` = '.(int)$user->id);
        $db->setQuery($q);
        $db->query();

        $data->current_profile = 1;

        $table = new PlgSloginProfilesTable($db);
        $table->load();
        $table->bind($data);
        $table->store();
    }

    private function updateCurrentProfile($user, $provider)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__plg_slogin_profile');
        $q->set('`current_profile` = 0');
        $q->where('`user_id` = '.(int)$user->id);
        $db->setQuery($q);
        $db->query();

        $q = $db->getQuery(true);
        $q->update('#__plg_slogin_profile');
        $q->set('`current_profile` = 1');
        $q->where('`user_id` = '.(int)$user->id);
        $q->where('`provider` = '.$db->quote($provider));
        $db->setQuery($q);
        $db->query();
    }


    private function issetProfile($user, $provider)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('COUNT(*)');
        $q->from('#__plg_slogin_profile');
        $q->where('`user_id` = '.(int)$user->id);
        $q->where('`provider` = '.$db->quote($provider));
        $db->setQuery($q);
        $res = $db->loadResult();
        return $res > 0;
    }


    private function updateAvatar($user, $provider, $file_input, $info)
    {
        if(empty($file_input)) return false;

        //максимальная ширина и высота для генерации изображения
        $w_o = $this->params->get('imgparam', 50);
        $h_o= $this->params->get('imgparam', 50);
        $this->params->set('enable_geo', 0);
        $id = isset($info->id) ? $info->id : $info->uid;
        $file_output = $provider . '_' . $id . '.jpg';


        $time = time();
        $rootfolder = $this->params->get('rootfolder', 'images/avatar');
        $output_path = JPATH_ROOT . '/' . $rootfolder . '/';
        $output_name = $output_path . $file_output;

        //Если файл существует и время замены не подошло возвращаем статус 'ok'
        if (is_file($output_name)) {
            if (filemtime($output_name) > ($time - $this->params->get('updatetime', 86400))) {
                return true;
            }
        }
        if($this->createAvatar($file_input, $file_output, $w_o, $h_o)){
            if($this->updateAvatarDB($user, $provider, $file_output)){
                return true;
            }
        }
        return false;
    }

    private function updateAvatarDB($user, $provider, $file_output)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__plg_slogin_profile');
        $q->set('`avatar` = '.$db->quote($file_output));
        $q->where('`user_id` = '.(int)$user->id);
        $q->where('`provider` = '.$db->quote($provider));
        $db->setQuery($q);
        if($db->query())
            return true;
        else
            return false;
    }

    /**
     * Метод для генерации изображения
     * @param string $url    УРЛ изображения
     * @param string $file_output    Название изображения для сохранения
     * @param int $w_o, $h_o    Максимальные ширина и высота генерируемого изображения
     * @return string    Результат выполнения false - изображения нет, up - успешно записали и нужно обновиться, ok - изображение существует и не требует модификации
     */
    private function createAvatar($file_input, $file_output, $width, $height)
    {

        //Если источник не указан
        if (!$file_input) return false;

        //папка для работы с изображением и качество сжатия
        $rootfolder = $this->params->get('rootfolder', 'images/avatar');
        $img_quality = $this->params->get('img_quality', 80);

        //если папка для складирования аватаров не существует создаем ее
        if (!JFolder::exists(JPATH_ROOT . '/' . $rootfolder)) {
            JFolder::create(JPATH_ROOT . '/' . $rootfolder);
            file_put_contents(JPATH_ROOT . '/' . $rootfolder . '/index.html', '');
        }

        // Генерируем имя tmp-изображения
        $tmp_name = JPATH_ROOT . '/tmp/' . $file_output;

        $output_path = JPATH_ROOT . '/' . $rootfolder . '/';
        $output_name = $output_path . $file_output;

        //заузка файла
        $uploaded = $this->upload($file_input, $tmp_name);

        if ($uploaded)
        {
            $image = new JImage($tmp_name);
            $image->resize($width, $height, false, JImage::SCALE_INSIDE);
            $image->toFile($output_name, IMAGETYPE_JPEG, array('quality'=>$img_quality));
            unlink($tmp_name);
        }

        $ret = (JFile::exists($output_name)) ? true : false;
        return $ret;
    }

    /** Загрузка файла с другого сервера
     * @param $from - путь до источника
     * @param $to - путь до локального файла
     * @return bool
     */
    private function upload($from, $to)
    {
        if (!function_exists('curl_init') || empty($from) || empty($to)) {
            return false;
        }
        $fp=fopen($to,"w");//создаем пустой файл
        if($fp === false){
            return false;
        }
        fclose($fp);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $from);//запускаем сеанс curl
        $fp=fopen($to,"w+");//открываем файл для записи
        if($fp === false){
            curl_close ($ch);//завершаем сеанс curl
            return false;
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);// записываем в файл
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_exec ($ch);//выполняем команды curl
        curl_close ($ch);//завершаем сеанс curl
        fclose ($fp);//закрываем файл

        if(filesize($to)>0){
            return true;
        }

        $file_input = $this->openHttp($from);
        if($file_input) {
            file_put_contents( $to, $file_input );
            return true;
        }
        return false;
    }

    function openHttp($url, $method = false, $params = null) {

        if (!function_exists('curl_init')) {
            die('ERROR: CURL library not found!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $method);
        if ($method == true && isset($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
            'Content-Length: '.strlen($params),
            'Cache-Control: no-store, no-cache, must-revalidate',
            "Expires: " . date("r")
        ));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
