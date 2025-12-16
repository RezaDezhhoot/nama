<?php

namespace App\Enums;

enum StatisticType: string
{
    case TOTAL_REQUESTS = "total_requests";
    case TOTAL_REPORTS = "total_reports";

    case TOTAL_MOSQUE_REQUESTS = "total_mosque_requests";
    case TOTAL_MOSQUE_REPORTS = "total_mosque_reports";

    case TOTAL_SCHOOL_REQUESTS = "total_school_requests";
    case TOTAL_SCHOOL_REPORTS = "total_school_reports";

    case TOTAL_CENTER_REQUESTS = "total_center_requests";
    case TOTAL_CENTER_REPORTS = "total_center_reports";

    case TOTAL_UNIVERSITY_REQUESTS = "total_university_requests";
    case TOTAL_UNIVERSITY_REPORTS = "total_university_reports";

    case TOTAL_GARDEN_REQUESTS = "total_garden_requests";
    case TOTAL_GARDEN_REPORTS = "total_garden_reports";

    case TOTAL_HALL_REQUESTS = "total_hall_requests";
    case TOTAL_HALL_REPORTS = "total_hall_reports";

    case TOTAL_STADIUM_REQUESTS = "total_stadium_requests";
    case TOTAL_STADIUM_REPORTS = "total_stadium_reports";

    case TOTAL_DARUL_QURAN_REQUESTS = "total_darul_quran_requests";
    case TOTAL_DARUL_QURAN_REPORTS = "total_darul_quran_reports";

    case TOTAL_CULTURAL_INSTITUTE_REQUESTS = "total_cultural_institute_requests";
    case TOTAL_CULTURAL_INSTITUTE_REPORTS = "total_cultural_institute_reports";

    case TOTAL_SEMINARY_REQUESTS = "total_seminary_requests";
    case TOTAL_SEMINARY_REPORTS = "total_seminary_reports";

    case TOTAL_QURANIC_CENTER_REQUESTS = "total_quranic_center_requests";
    case TOTAL_QURANIC_CENTER_REPORTS = "total_quranic_center_reports";
}
