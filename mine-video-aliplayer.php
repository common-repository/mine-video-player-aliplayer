<?php
/*
Plugin Name: Mine Video Player - Aliplayer
Plugin URI: https://www.zwtt8.com/plugins/2020-11-27/mine-video-player-aliplayer/
Description: Aliplayer支持阿云视频点播/mp4/m3u8/直播，需安装<a href="https://wordpress.org/plugins/mine-video/">Mine Video Player</a>一起使用，支持播放列表。
Version: 1.0.10
Author: mine27
Author URI: https://www.zwtt8.com/
*/
if(!defined('ABSPATH'))exit;
define('MINEVIDEOALIPLAYER_URL', plugins_url('', __FILE__));
define('MINEVIDEOALIPLAYER_VERSION', '1.0.10');

//aliplayer_vod
function mine_video_jxapistr_aliplayer_vod($jxapistr_cur, $typearr, $jxapi_cur, $r, $ti){
	global $current_user;
	$ajaxurl						= esc_url(admin_url('admin-ajax.php'));
	$wp_create_nonce				= wp_create_nonce('mcv-aliyunvod-' . $current_user->ID);
	if(strtolower($jxapi_cur) == 'aliplayer_vod'){
		$autoheight = '';
		if(isset(MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight']) && MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight'] == 'true'){
			$autoheight = 'var mine_pl = document.getElementById(\'playleft_\'+pid);mine_pl.style.height=\'auto\';mine_pl.getElementsByTagName(\'video\')[0].style.height = \'auto\'';
		}
        $aliconfig = '{"id": "playleft_"+pid,"vid": cur.video, "playauth": "", "qualitySort": "asc","format": "mp4","mediaType": "video","enciryptType": 1,"width": "100%","height": "100%","autoplay": '.(@MINEVIDEO_SETTINGS['aliplayerconfig']['autoplay']? 'true' : 'false').',"isLive": false,"rePlay": false,"playsinline": false,"preload": false,"controlBarVisibility": "hover","useH5Prism": true}';
        if(defined('MINECLOUDVOD_SETTINGS')){
            $pctrl = ["name" => "controlBar", "align" => "blabs", "x" => 0, "y" => 0,
                'children' => []
            ];
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['progress']){
                $pctrl['children'][] = ["name" => "progress", "align" => "blabs", "x" => 0, "y" => 44];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['playButton']){
                $pctrl['children'][] = ["name" => "playButton", "align" => "tl", "x" => 15, "y" => 12];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['timeDisplay']){
                $pctrl['children'][] = ["name" => "timeDisplay", "align" => "tl", "x" => 10, "y" => 7];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['fullScreenButton']){
                $pctrl['children'][] = ["name" => "fullScreenButton", "align" => "tr", "x" => 10, "y" => 12];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['subtitle']){
                $pctrl['children'][] = ["name" => "subtitle", "align" => "tr", "x" => 15, "y" => 12];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['setting']){
                $pctrl['children'][] = ["name" => "setting", "align" => "tr", "x" => 15, "y" => 12];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['volume']){
                $pctrl['children'][] = ["name" => "volume", "align" => "tr", "x" => 5, "y" => 10];
            }
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['snapshot']){
                $pctrl['children'][] = ["name" => "snapshot", "align" => "tr", "x" => 10, "y" => 12];
            }
            $pskin = array(
                ["name" => "H5Loading", "align" => "cc"],
                ["name" => "errorDisplay", "align" => "tlabs", "x" => 0, "y" => 0],
                ["name" => "infoDisplay"],
                ["name" => "tooltip", "align" => "blabs", "x" => 0, "y" => 56],
                ["name" => "thumbnail"],
                $pctrl,
            );
            if(MINECLOUDVOD_SETTINGS['aliplayerconfig']['bigPlayButton']){
                $pskin[] = ["name" => "bigPlayButton", "align" => "blabs", "x" => 30, "y" => 80];
            }
            $pconfig = array(
                "id"        => 'playleft_'.$r,
                "vid"       => '',
                "playauth"       => '',
                "qualitySort"       => 'asc',
                "format"       => 'mp4',
                "mediaType"       => 'video',
                'encryptType'       => 1,
                "width"       => '100%',
                "height"       => '100%',
                "isLive"       => false,
                "playsinline"       => false,
                "useH5Prism"       => true,

                "autoplay"       => MINECLOUDVOD_SETTINGS['aliplayerconfig']['autoplay']? true : false,
                "rePlay"       => MINECLOUDVOD_SETTINGS['aliplayerconfig']['rePlay']? true : false,
                "preload"       => MINECLOUDVOD_SETTINGS['aliplayerconfig']['preload']? true : false,
                "controlBarVisibility"       => isset(MINECLOUDVOD_SETTINGS['aliplayerconfig']['controlBarVisibility'])?MINECLOUDVOD_SETTINGS['aliplayerconfig']['controlBarVisibility']:'hover',
                "skinLayout" => $pskin
            );
            $aliconfig = json_encode($pconfig);
        }
        
		wp_enqueue_style('mine_aliplayer_css', 'https://g.alicdn.com/de/prismplayer/2.9.1/skins/default/aliplayer-min.css', MINEVIDEOALIPLAYER_URL, false);
		wp_enqueue_script('mine_aliplayer', 'https://g.alicdn.com/de/prismplayer/2.9.1/aliplayer-min.js',  MINEVIDEOALIPLAYER_URL, MINEVIDEOALIPLAYER_VERSION , false );
		wp_add_inline_script('mine_aliplayer','var aliplayerconfig_'.$r.';var aliplayer_'.$r.';function mine_'.strtolower($jxapi_cur).'_'.$r.'(pid,cur){window.aliplayerconfig_'.$r.'='.$aliconfig.';  layui.use(["jquery"], function(){var $ = layui.jquery;$.post("'.$ajaxurl.'",{"action":"mcv_alivod_upload","nonce":"'.$wp_create_nonce.'","op":"playauth","vid":cur.video}, function (data) {if(!window.aliplayer_'.$r.'){if(data.hls==false){window.aliplayerconfig_'.$r.'.playauth=data.playauth;window.aliplayerconfig_'.$r.'.vid=data.vid;}else{window.aliplayerconfig_'.$r.'.format="m3u8";window.aliplayerconfig_'.$r.'.source=data.hls;}window.aliplayer_'.$r.'=new Aliplayer(window.aliplayerconfig_'.$r.', function (player) {console.log("The aliplayer is created");});'.$autoheight.'}else{window.aliplayer_'.$r.'.replayByVidAndPlayAuth(data.vid,data.playauth);}}, \'json\');});}');
		return '<input type="hidden" id="mine_ifr_'.$typearr[$ti].'_'.$r.'" value=\''.$jxapi_cur.'\'/>';
	}
	return $jxapistr_cur;
}
add_filter('mine_video_jxapistr', 'mine_video_jxapistr_aliplayer_vod', 10, 5);
//aliplayer_live
function mine_video_jxapistr_aliplayer_live($jxapistr_cur, $typearr, $jxapi_cur, $r, $ti){
	global $current_user;
	$ajaxurl						= esc_url(admin_url('admin-ajax.php'));
	$wp_create_nonce				= wp_create_nonce('minevideo-aliyunvod-' . $current_user->ID);
	if(strtolower($jxapi_cur) == 'aliplayer_live'){
		$autoheight = '';
		if(isset(MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight']) && MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight'] == 'true'){
			$autoheight = 'var mine_pl = document.getElementById(\'playleft_\'+pid);mine_pl.style.height=\'auto\';mine_pl.getElementsByTagName(\'video\')[0].style.height = \'auto\'';
		}
		wp_enqueue_style('mine_aliplayer_css', 'https://g.alicdn.com/de/prismplayer/2.9.1/skins/default/aliplayer-min.css', MINEVIDEOALIPLAYER_URL, false);
		wp_enqueue_script('mine_aliplayer', 'https://g.alicdn.com/de/prismplayer/2.9.1/aliplayer-min.js',  MINEVIDEOALIPLAYER_URL, MINEVIDEOALIPLAYER_VERSION , false );
		wp_add_inline_script('mine_aliplayer','var aliplayerconfig_'.$r.';var aliplayer_'.$r.';function mine_'.strtolower($jxapi_cur).'_'.$r.'(pid,cur){aliplayerconfig_'.$r.'={"id": "playleft_"+pid,"source": cur.video, "width": "100%","height": "100%","autoplay": '.(@MINEVIDEO_SETTINGS['aliplayerconfig']['autoplay']? 'true' : 'false').',"isLive": true,"rePlay": false,"playsinline": false,"preload": false,"controlBarVisibility": "hover","useH5Prism": true};  if(!window.aliplayer_'.$r.'){window.aliplayer_'.$r.'=new Aliplayer(aliplayerconfig_'.$r.', function (player) {console.log("The aliplayer is created");});'.$autoheight.'}else{window.aliplayer_'.$r.'.loadByUrl(cur.video);}}');
		return '<input type="hidden" id="mine_ifr_'.$typearr[$ti].'_'.$r.'" value=\''.$jxapi_cur.'\'/>';
	}
	return $jxapistr_cur;
}
add_filter('mine_video_jxapistr', 'mine_video_jxapistr_aliplayer_live', 10, 5);
//aliplayer
function mine_video_jxapistr_aliplayer($jxapistr_cur, $typearr, $jxapi_cur, $r, $ti){
	global $current_user;
	$danmuconfig = '';
    $components = '';
	if(isset(MINEVIDEO_SETTINGS['mvp_aliplayer_luping']) && $current_user->ID>0){
		$luping = MINEVIDEO_SETTINGS['mvp_aliplayer_luping'];
		if(@$luping['status'] == 'true'){
			wp_enqueue_script('mine_aliplayer_components', MINEVIDEOALIPLAYER_URL.'/components/aliplayercomponents-1.0.5.min.js',  MINEVIDEOALIPLAYER_URL, MINEVIDEOALIPLAYER_VERSION , false );
			$danmustr = str_replace(array('{userid}', '{username}'), array($current_user->ID, $current_user->user_login), $luping['content']);
			$danmuconfig = ',components: [{name: "BulletScreenComponent",type: AliPlayerComponent.BulletScreenComponent,args: ["'.$danmustr.'", {fontSize: "16px", color: "#"+Math.random().toString(16).substr(2, 6).toUpperCase()}, "random"]}]';
            
            $components .= '{name: "BulletScreenComponent",type: AliPlayerComponent.BulletScreenComponent,args: ["'.$danmustr.'", {fontSize: "16px", color: "#"+Math.random().toString(16).substr(2, 6).toUpperCase()}, "random"]},';
		}
	}
	$autoheight = '';
	if(isset(MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight']) && MINEVIDEO_SETTINGS['mvp_aliplayer_autoheight'] == 'true'){
		$autoheight = 'var mine_pl = document.getElementById(\'playleft_\'+pid);mine_pl.style.height=\'auto\';mine_pl.getElementsByTagName(\'video\')[0].style.height = \'auto\'';
	}
    $aliconfig = '{"id": "playleft_'.$r.'","source": cur.video, "width": "100%","height": "100%","autoplay": '.(@MINEVIDEO_SETTINGS['aliplayerconfig']['autoplay']? 'true' : 'false').',"isLive": false,"rePlay": false,"playsinline": true,"preload": true,"controlBarVisibility": "hover","useH5Prism": true'.$danmuconfig.'}';
    if(isset(MINEVIDEO_SETTINGS['aliplayerconfig']['bigPlayButton'])){
        $pctrl = ["name" => "controlBar", "align" => "blabs", "x" => 0, "y" => 0,
            'children' => []
        ];
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['progress']){
            $pctrl['children'][] = ["name" => "progress", "align" => "blabs", "x" => 0, "y" => 44];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['playButton']){
            $pctrl['children'][] = ["name" => "playButton", "align" => "tl", "x" => 15, "y" => 12];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['timeDisplay']){
            $pctrl['children'][] = ["name" => "timeDisplay", "align" => "tl", "x" => 10, "y" => 7];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['fullScreenButton']){
            $pctrl['children'][] = ["name" => "fullScreenButton", "align" => "tr", "x" => 10, "y" => 12];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['subtitle']){
            $pctrl['children'][] = ["name" => "subtitle", "align" => "tr", "x" => 15, "y" => 12];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['setting']){
            $pctrl['children'][] = ["name" => "setting", "align" => "tr", "x" => 15, "y" => 12];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['volume']){
            $pctrl['children'][] = ["name" => "volume", "align" => "tr", "x" => 5, "y" => 10];
        }
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['snapshot']){
            $pctrl['children'][] = ["name" => "snapshot", "align" => "tr", "x" => 10, "y" => 12];
        }
        $pskin = array(
            ["name" => "H5Loading", "align" => "cc"],
            ["name" => "errorDisplay", "align" => "tlabs", "x" => 0, "y" => 0],
            ["name" => "infoDisplay"],
            ["name" => "tooltip", "align" => "blabs", "x" => 0, "y" => 56],
            ["name" => "thumbnail"],
            $pctrl,
        );
        if(MINEVIDEO_SETTINGS['aliplayerconfig']['bigPlayButton']){
            $pskin[] = ["name" => "bigPlayButton", "align" => "blabs", "x" => 30, "y" => 80];
        }
        $pconfig = array(
            "id"        => 'playleft_'.$r,
            "vid"       => '',
            "playauth"       => '',
            "qualitySort"       => 'asc',
            "format"       => 'mp4',
            "mediaType"       => 'video',
            'encryptType'       => 1,
            "width"       => '100%',
            "height"       => '100%',
            "isLive"       => false,
            "playsinline"       => false,
            "useH5Prism"       => true,
            "autoplay"       => MINEVIDEO_SETTINGS['aliplayerconfig']['autoplay']? true : false,
            "rePlay"       => MINEVIDEO_SETTINGS['aliplayerconfig']['rePlay']? true : false,
            "preload"       => MINEVIDEO_SETTINGS['aliplayerconfig']['preload']? true : false,
            "controlBarVisibility"       => isset(MINEVIDEO_SETTINGS['aliplayerconfig']['controlBarVisibility'])?MINEVIDEO_SETTINGS['aliplayerconfig']['controlBarVisibility']:'hover',
            "skinLayout" => $pskin
        );
        $aliconfig = json_encode($pconfig);
    }
	if(strtolower($jxapi_cur) == 'aliplayer'){
		wp_enqueue_style('mine_aliplayer_css', 'https://g.alicdn.com/de/prismplayer/2.9.1/skins/default/aliplayer-min.css', MINEVIDEOALIPLAYER_URL, false);
		wp_enqueue_script('mine_aliplayer', 'https://g.alicdn.com/de/prismplayer/2.9.1/aliplayer-min.js',  MINEVIDEOALIPLAYER_URL, MINEVIDEOALIPLAYER_VERSION , false );
		wp_add_inline_script('mine_aliplayer','var aliplayerconfig_'.$r.';var aliplayer_'.$r.';function mine_'.strtolower($jxapi_cur).'_'.$r.'(pid,cur){aliplayerconfig_'.$r.'='.$aliconfig.';aliplayerconfig_'.$r.'.source=cur.video;aliplayerconfig_'.$r.'.components=['.$components.'];  if(!window.aliplayer_'.$r.'){window.aliplayer_'.$r.'=new Aliplayer(aliplayerconfig_'.$r.', function (player) {console.log("The aliplayer is created");});'.$autoheight.'}else{window.aliplayer_'.$r.'.loadByUrl(cur.video);}}');
		return '<input type="hidden" id="mine_ifr_'.$typearr[$ti].'_'.$r.'" value=\''.$jxapi_cur.'\'/>';
	}
	return $jxapistr_cur;
}
add_filter('mine_video_jxapistr', 'mine_video_jxapistr_aliplayer', 10, 5);

function mine_video_no_active_notice_aliplayer(){
	include_once(ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active( 'mine-video/mine-video.php' ) ) {
		echo '<tr class="plugin-update-tr active"><td colspan="3" class="plugin-update colspanchange"><div class="notice-error notice inline notice-warning notice-alt"><p>请先安装并启用主插件 <a href="'.esc_url(admin_url('plugin-install.php')).'?tab=plugin-information&plugin=mine-video&TB_iframe=true&width=772&height=909" class="thickbox open-plugin-details-modal" aria-label="关于Mine Video Player的更多信息" data-title="Mine Video Player">Mine Video Player</a>。</p></div></td></tr>';
	}
}
add_action( "after_plugin_row_mine-video-aliplayer/mine-video-aliplayer.php", 'mine_video_no_active_notice_aliplayer', 10, 2 );



add_action('mine_video_creatSection', 'mvp_setting_aliplayer', 10, 1);
function mvp_setting_aliplayer($prefix){
    if(!class_exists('MCSF')) return;
	MCSF::createSection( $prefix, array(
    'title'  => 'AliPlayer配置',
    'icon'   => 'fab fa-adn',
    'fields' => array(
        array(
        'type'    => 'submessage',
        'style'   => 'success',
        'content' => '
        <p>欢迎使用 Mine Video Player
        ',
        ),
        array(
            'id'    => 'mvp_aliplayer_autoheight',
            'type'  => 'select',
            'title' => '高度自适应',
            'options'     => array(
                'true'      => '启用',
                'false'      => '禁用',
            ),
            'default' => 'false'
        ),
        array(
            'id'    => 'mvp_aliplayer_autoplay',
            'type'  => 'select',
            'title' => '自动播放',
            'options'     => array(
                'true'      => '是',
                'false'      => '否',
            ),
            'default' => 'false'
        ),
        array(
            'id'        => 'aliplayerconfig',
            'type'      => 'fieldset',
            'title'     => '配置',
            'fields'    => array(
                array(
                    'id'    => 'autoplay',
                    'type'  => 'switcher',
                    'title' => '自动播放',
                    'help' => '播放器是否自动播放，在移动端autoplay属性会失效。Safari11不会自动开启自动播放<a href="https://h5.m.youku.com//ju/safari11guide.html" target="_blank">如何开启</a>',
                    'text_on'    => '启用',
                    'text_off'   => '禁用',
                    'default' => false
                ),
                array(
                    'id'    => 'preload',
                    'type'  => 'switcher',
                    'title' => '自动加载',
                    'help' => '播放器自动加载，目前仅h5可用',
                    'text_on'    => '启用',
                    'text_off'   => '禁用',
                    'default' => false
                ),
                array(
                    'id'    => 'rePlay',
                    'type'  => 'switcher',
                    'title' => '循环播放',
                    'text_on'    => '启用',
                    'text_off'   => '禁用',
                    'default' => false
                ),
                array(
                    'id'    => 'controlBarVisibility',
                    'type'  => 'select',
                    'options'     => array(
                        'hover' => 'hover',
                        'click' => 'click',
                        'always' => 'always',
                    ),
                    'attributes' => array(
                      'style'    => 'min-width: 100px;'
                    ),
                    'default'     => 'hover',
                    'title' => '控制面板的显示方式',
                ),
                array(
                    'id'    => 'bigPlayButton',
                    'type'  => 'switcher',
                    'title' => '大播放按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'success',
                    'content' => '
                    <p>如下是控制栏属性设置</p>
                    ',
                    ),
                array(
                    'id'    => 'progress',
                    'type'  => 'switcher',
                    'title' => '进度条',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'playButton',
                    'type'  => 'switcher',
                    'title' => '播放按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'timeDisplay',
                    'type'  => 'switcher',
                    'title' => '时间',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'fullScreenButton',
                    'type'  => 'switcher',
                    'title' => '全屏按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'setting',
                    'type'  => 'switcher',
                    'title' => '设置按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'volume',
                    'type'  => 'switcher',
                    'title' => '音量按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => true
                ),
                array(
                    'id'    => 'subtitle',
                    'type'  => 'switcher',
                    'title' => '字幕按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => false
                ),
                array(
                    'id'    => 'snapshot',
                    'type'  => 'switcher',
                    'title' => '截图按钮',
                    'text_on'    => '显示',
                    'text_off'   => '隐藏',
                    'default' => false
                ),
            ),
        ),
        array(
            'id'        => 'mvp_aliplayer_luping',
            'type'      => 'fieldset',
            'title'     => '防录屏',
            'fields'    => array(
                array(
                'id'    => 'status',
                'type'  => 'radio',
                'title' => '状态',
                'inline' => true,
                'options'    => array(
                    'true'	=> '启用',
                    'false'	=> '禁用'
                ),
                ),
                array(
                'id'    => 'content',
                'type'  => 'text',
                'title' => '内容',
                'default' => '用户：{userid}',
                'desc' => '支持标签{username}{userid}'
                ),
            )
        ),
    )
    ));
}
?>