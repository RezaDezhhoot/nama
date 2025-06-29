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
}
