<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\Controller;
use ScoutUnitsList\Model\User;
use WP_User;

/**
 * Admin user controller
 */
class UserController extends Controller
{
    /**
     * Form
     *
     * @param WP_User $wpUser WP user
     */
    public function form(WP_User $wpUser)
    {
        \wp_enqueue_media();

        $userRepository = $this->get('repository.user');
        $user = $userRepository->createModelFromWpUser($wpUser);

        $this->getView(current_user_can('promote_users') ? 'Admin/Users/AdminForm' : 'Admin/Users/UserForm', [
            'config' => $this->get('manager.config')
                ->get(),
            'publishEmails' => $userRepository->getPublishEmails(),
            'sexes' => [
                User::SEX_FEMALE => __('Female', 'scout-units-list'),
                User::SEX_MALE => __('Male', 'scout-units-list'),
            ],
            'user' => $user,
        ])->render();
    }

    /**
     * Update
     * 
     * @param int $userId user ID
     */
    public function update($userId)
    {
        $userCanPromoteUsers = current_user_can('promote_users');
        if (get_current_user_id() == $userId || $userCanPromoteUsers) {
            $userRepository = $this->get('repository.user');
            $user = $userRepository->getOneBy([
                'id' => $userId,
            ]);
            if (isset($user)) {
                $params = $this->request->request;
                $user->setPublishEmail($params->getInt('sul_publish_email'))
                    ->setDuty($params->getString('sul_duty'));
                if ($userCanPromoteUsers) {
                    $user->setPhotoId($this->checkPhoto($params->getInt('sul_photo_id')))
                        ->setGrade($params->getString('sul_grade'))
                        ->setSex($params->getString('sul_sex'));
                }
                $userRepository->save($user);
            }
        }
    }

    /**
     * Check photo
     *
     * @param int $photoId photo ID
     *
     * @return int|null
     */
    private function checkPhoto($photoId)
    {
        if ($photoId < 1) {
            return null;
        }

        $mimeType = \get_post_mime_type($photoId);
        if (strpos($mimeType, 'image/') !== 0) {
            return null;
        }

        return $photoId;
    }
}
