<?php

namespace App\Http\Interfaces;

Interface CourseMaterialServiceInterface
{
    public function getAllUserCourseMateriels($userId);

    public function getCourseMaterialById($courseMaterialId);

    public function deleteCourseMaterialById($courseMaterialId);

    public function checkIfUserIsCourseOwner($userId, $courseMaterialId);

    public function findRecommendedCourses($availabilityContext);
}