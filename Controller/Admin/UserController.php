<?php

namespace ScoutUnitsList\Controller\Admin;

use ScoutUnitsList\Controller\Controller;
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

        $this->getView(current_user_can('promote_users') ? 'Admin/Users/Form' : 'Admin/Users/Show', [
            'publishEmails' => $userRepository->getPublishEmails(),
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
        if (current_user_can('promote_users')) {
            $userRepository = $this->get('repository.user');
            $user = $userRepository->getOneBy([
                'id' => $userId,
            ]);
            if (isset($user)) {
                $params = $this->request->request;
                $user->setPublishEmail($params->getInt('sul_publish_email'))
                    ->setGrade($params->getString('sul_grade'))
                    ->setDuty($params->getString('sul_duty'));
                $userRepository->save($user);
            }
        }
    }
}