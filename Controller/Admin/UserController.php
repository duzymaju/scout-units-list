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
        $userRepository = $this->get('repository.user');
        $user = $userRepository->createModelFromWpUser($wpUser);

        $this->getView(current_user_can('promote_users') ? 'Admin/Users/AdminForm' : 'Admin/Users/UserForm', [
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
                    $user->setGrade($params->getString('sul_grade'))
                        ->setResponsibilities($params->getString('sul_responsibilities'))
                        ->setSex($params->getString('sul_sex'));
                }
                $userRepository->save($user);
            }
        }
    }
}
