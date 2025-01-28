<?php

use App\Enums\OperatorRole;
use App\Enums\UserRole;
use App\Models\Setttings;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

function array_value_recursive($key, array $arr): array
{
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) $val[] = $v;
    });
    return $val;
}


function emptyToNull($value = "")
{
    if (empty($value))
        return null;

    return $value;
}


function mimeTypeToExtension($mimeType): bool|string
{
    // A basic array mapping common MIME types to file extensions
    $mimeMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/bmp' => 'bmp',
        'image/svg+xml' => 'svg',
        'image/tiff' => 'tiff',
        'application/pdf' => 'pdf',
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'text/plain' => 'txt',
        'text/html' => 'html',
        'application/json' => 'json',
        'application/javascript' => 'js',
        'application/xml' => 'xml',
        'audio/mpeg' => 'mp3',
        'video/mp4' => 'mp4',
        'video/x-msvideo' => 'avi',
        'application/octet-stream' => 'bin', // Generic binary data
    ];

    // Return the corresponding file extension if found, otherwise return false
    return $mimeMap[$mimeType] ?? false;
}


function dateConverter($date = null,$mode = 'j' , $fromFormat = 'Y-m-d'): ?string
{
    if (!empty($date)) {
        return $mode == 'j' ? Jalalian::fromDateTime(Carbon::make($date))->format('Y-m-d H:i:s')
            : Jalalian::fromFormat($fromFormat, convert2english($date))->toCarbon()->format('Y-m-d H:i:s');
    }
    return null;
}


function isVideo($filePath): bool
{
    $videoExtensions = config("site.files.video.formats");
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    return in_array($fileExtension, $videoExtensions);
}

function isImage($filePath): bool
{
    $imageExtensions = config("site.files.image.formats");
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    return in_array($fileExtension, $imageExtensions);
}

function isAudio($filePath): bool
{
    $imageExtensions = config("site.files.audio.formats");
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    return in_array($fileExtension, $imageExtensions);
}

/**
 * @throws ReflectionException
 */
function getClassShortName($class): string
{
    $reflect = new ReflectionClass($class);
    return $reflect->getShortName();
}

function isMedia($path): bool
{
    return isImage($path) || isVideo($path) || isAudio($path);
}

function convert2english($string): array|string
{
    $newNumbers = range(0, 9);
    // 1. Persian HTML decimal
    $persianDecimal = array('&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;');
    // 2. Arabic HTML decimal
    $arabicDecimal = array('&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;');
    // 3. Arabic Numeric
    $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    // 4. Persian Numeric
    $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');

    $string =  str_replace($persianDecimal, $newNumbers, $string);
    $string =  str_replace($arabicDecimal, $newNumbers, $string);
    $string =  str_replace($arabic, $newNumbers, $string);
    return str_replace($persian, $newNumbers, $string);
}

function persian_date($date): ?string
{
    return $date ? Jalalian::forge($date)->format('%A, %d %B %Y') : null;
}


function getSetting(...$name)
{
    if (sizeof($name) == 1) {
        return Setttings::getSingleRow($name[0]);
    }

    return Setttings::query()->whereIn('name',$name)->pluck('value','name');
}


function isAdmin(): bool
{
    return in_array(auth()->user()->role , [UserRole::ADMIN, UserRole::SUPER_ADMIN]);
}

function isOperator(): bool
{
    return in_array(auth()->user()->nama_role , OperatorRole::cases());
}
