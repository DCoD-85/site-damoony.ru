<!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> -->
GDEF_PRO_INSTALL 1

GDEF_DOMAIN
GDEF_DOMAIN_WEB						www.site-def.ru
GDEF_DOMAIN_WEB_IMAGES			images.site-def.ru
GDEF_DOMAIN_WEB_AUDIO				audio.site-def.ru
GDEF_DOMAIN_WEB_VIDEO				video.site-def.ru
GDEF_DOMAIN_WEB_DOCUMENTS	doc.site-def.ru
GDEF_DOMAIN_LOCAL						site-def.ru

GDEF_FORMNAME form

GDEF_USES uses

// Префикс для базы данных
__DB__PREFIX prfx_

__ACT__REGS 1
__ACT__AUTH 1

__PROFILE_POST__P_MAX 10

// Базовый путь
GDEF_BASENAME Z:\home\site-cantora.ru\www\
GDEF_BASENAME_MASK  \***\***\***\

// Количество минут бездействия пользователя, когда он считается в режиме ONLINE
__USER_ONLINE_TIME_MAX_MIN 5

// Локальный и WEB путь к файлам
GDEF_BASEPATH /home/

//
// FILES
//

// Локальный и WEB путь к файлам
GDEF_FILES_LPATH site-def.ru/
GDEF_FILES_WPATH www.site-def/..images/

// Локальный и WEB путь к файлам [images | audio | video]
GDEF_F_IMAGES_ON_HOMEHOST 1
GDEF_F_IMAGES_LPATH site-def.ru/www/..images/
GDEF_F_IMAGES_WPATH /..images/
GDEF_F_IMAGES_FOLDER_PRFX usr

GDEF_F_IMAGES_PRFX_ORG					org.
GDEF_F_IMAGES_PRFX_ORG_W			1200
GDEF_F_IMAGES_PRFX_ORG_W2H			0
GDEF_F_IMAGES_PRFX_ICO					ico.
GDEF_F_IMAGES_PRFX_ICO_W				204
GDEF_F_IMAGES_PRFX_ICO_W2H			1
GDEF_F_IMAGES_PRFX_MINW				minw.
GDEF_F_IMAGES_PRFX_MINW_W			100
GDEF_F_IMAGES_PRFX_MINW_W2H		0
GDEF_F_IMAGES_PRFX_MINH				minh.
GDEF_F_IMAGES_PRFX_MINH_W			190
GDEF_F_IMAGES_PRFX_MINH_W2H		0
GDEF_F_IMAGES_PRFX_MAX					max.
GDEF_F_IMAGES_PRFX_MAX_W			800
GDEF_F_IMAGES_PRFX_MAX_W2H			1


GDEF_F_AUDIO_ON_HOMEHOST 1
GDEF_F_AUDIO_LOCAL_PATH home/site-cantora.ru/www/..audio/
GDEF_F_AUDIO_USER_PATH www.site-cantora.ru/..audio/

GDEF_F_VIDEO_ON_HOMEHOST 1
GDEF_F_VIDEO_LOCAL_PATH home/site-cantora.ru/www/..video/
GDEF_F_VIDEO_USER_PATH www.site-cantora.ru/..video/

GDEF_F_FILES_ON_HOMEHOST 1
GDEF_F_FILES_LPATH home/site-cantora.ru/www/..files/
GDEF_F_FILES_WPATH www.site-cantora.ru/..files/
GDEF_F_FILES_FOLDER_PRFX file_

GDEF_FSIZE_IMAGE_MAX_SIZE 10000000
GDEF_FSIZE_VIDEO_MAX_SIZE 5242880
GDEF_FSIZE_AUDIO_MAX_SIZE 10485760

// ( simple | hard )
__IMG_SPEC_REDRAW hard

// Формат изображения
__IMG__FORMAT jpeg
// Ширина изображения
__IMG__PROFILE_INFO_AVATAR_MAIN_WIDTH 200

// 
// Новости
// 


// Изображения:
// Разрешенные типы файлов через знак разделения |
__NEWS__IMG__ACCEPTS_MIMETYPES image/gif|image/jpeg|image/png|image/pjpeg|image/x-png
// максимальный размер | максимальная ширина в пикселях | 
__NEWS__IMG_STD_SIZE 2097152
__NEWS__IMG_STD_WIDTH_MAX 400
__NEWS__IMG_STD_WIDTH_IMPORTANT 1
__NEWS__IMG_SMALL_SIZE 2097152
__NEWS__IMG_SMALL_WIDTH_MAX
__NEWS__IMG_SMALL_WIDTH_IMPORTANT
__NEWS__IMG_TOLIST_SIZE 2097152
__NEWS__IMG_TOLIST_WIDTH_MAX
__NEWS__IMG_TOLIST_WIDTH_IMPORTANT

//
// VERSION
//

VERSION_NUM 1.0.1
VERSION_START 21.12.2007
VERSION_UPDATE 21.12.2007
SITE_START
				