<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use \common\models\UserLanguage;
use common\models\Files;
use common\models\Language;
use yii\web\UploadedFile;
use common\models\UserDiploma;
use common\models\UserVerification;

class RegistrationController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionPerformer()
    {
        // set up new user/profile objects
        $user    = Yii::$app->getModule("user")->model("User", ["scenario" => "register"]);
        $profile = Yii::$app->getModule("user")->model("Profile");
        $userLanguage = new UserLanguage();
        $languages = Language::getExistLanguagesArray(); // all exists languages - ao array for widget
        $files = new Files();
       
        $post = Yii::$app->request->post();
        if ($user->load($post)) {
            $profile->load($post);
            // section ajax validate basic registration models
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user, $profile);
            }
            // section normal validate basic registration models
            if ($user->validate() && $profile->validate()) {
                $role = Yii::$app->getModule('user')->model('Role');
                $user->setRegisterAttributes($role::EXT_ROLE_PERFORMER, Yii::$app->request->userIP)->save(false);
                // section only photo uploads
                if (!is_null(UploadedFile::getInstanceByName('photo'))) {
                    $photo_id = $files->saveSingleImage($user->id);
                    $profile->photo = $photo_id;
                }
                $profile->setUser($user->id)->save(false);
                $this->afterRegister($user);
                $successText = Yii::t("user", "Successfully registered [ {displayName} ]", ["displayName" => $user->getDisplayName()]);
                $guestText = "";
                if (Yii::$app->user->isGuest) {
                    $guestText = Yii::t("user", " - Please check your email to confirm your account");
                }
                Yii::$app->session->setFlash("Register-success", $successText . $guestText);
            // section stars
                /* receive users languages and it`s ratings */
                if(!empty($post) && isset($post['UserLanguage'])){
                    $languages = $post['UserLanguage']['language'];
                    /* user language implementation process */
                    $this->userLanguageImplements($languages, $user->id);
                }
            // section diploma and ID upload/attach
                //Diploma
                if(!is_null(UploadedFile::getInstancesByName('cert'))){
                    $diplomaIdArray = $files->saveMultyImage($user->id, 'diploma', 'cert');
                    if(is_array($diplomaIdArray)){
                        UserDiploma::DiplomaAttachmentProcess($user->id, $diplomaIdArray);   
                    }
                }
                //ID
                if(!is_null(UploadedFile::getInstancesByName('vercode'))){
                    $verificationsIdArray = $files->saveMultyImage($user->id, 'verificationID', 'vercode');
                    if(is_array($verificationsIdArray)){
                        UserVerification::VerifycationAttachmentProcess($user->id, $verificationsIdArray);
                    }
                }
            }     
        }
        
        return $this->render('performer', ['user'=>$user, 'profile'=>$profile, 'userLanguage'=>$userLanguage, 'languages'=>$languages, 'files'=>$files]);
    }
    
    public function actionCustomer(){
        $user    = Yii::$app->getModule("user")->model("User", ["scenario" => "register"]);
        $profile = Yii::$app->getModule("user")->model("Profile");
        $post = Yii::$app->request->post();
         if ($user->load($post)) {
            $profile->load($post);
            // ajax validate registration
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user, $profile);
            }
            // section normal validate basic registration models
            if ($user->validate() && $profile->validate()) {
                $role = Yii::$app->getModule('user')->model('Role');
                $user->setRegisterAttributes($role::EXT_ROLE_CUSTOMER, Yii::$app->request->userIP)->save(false);
                $profile->setUser($user->id)->save(false);
                $this->afterRegister($user);
                $successText = Yii::t("user", "Successfully registered [ {displayName} ]", ["displayName" => $user->getDisplayName()]);
                $guestText = "";
                if (Yii::$app->user->isGuest) {
                    $guestText = Yii::t("user", " - Please check your email to confirm your account");
                }
                Yii::$app->session->setFlash("Register-success", $successText . $guestText);
            }
         }
        return $this->render('customer', ['user'=>$user, 'profile'=>$profile]);
    }
    
    protected function userLanguageImplements($choiseLanguages = array(), $user_id = NULL){
   
        if(empty($choiseLanguages) || is_null($user_id)){
            return 0;
        }
        foreach($choiseLanguages as $language){
            if(isset($language[0]) && (int)$language[1] !== 0 ){
                $model = new UserLanguage;
                $model->setAttributes([
                    'user_id'=>$user_id,
                    'language_id'=>$language[2],
                    'knowledge'=>$language[1],
                ]);
                $model->save();
            }
        }
    }
    
    

    protected function afterRegister($user)
    {
        /** @var \common\modules\user\models\UserKey $userKey */

        // determine userKey type to see if we need to send email
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        if ($user->status == $user::STATUS_INACTIVE) {
            $userKeyType = $userKey::TYPE_EMAIL_ACTIVATE;
        } elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userKeyType = $userKey::TYPE_EMAIL_CHANGE;
        } else {
            $userKeyType = null;
        }

        // check if we have a userKey type to process, or just log user in directly
        if ($userKeyType) {

            // generate userKey and send email
            $userKey = $userKey::generate($user->id, $userKeyType);
            if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error", "Failed to send email");
            }
        } else {
            Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
        }
    }
    
} 