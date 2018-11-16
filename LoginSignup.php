<?php
/**
 * Created by Doniyor Mamatkulov.
 * User: d.mamatkulov
 * Date: 16.11.2018
 * Time: 9:10
 */

namespace app\controllers;


class LoginSignup {

    public function actionSignup() {
        $regModel = new Users(['scenario'=>Users::SCENARIO_REGISTER]);
        if (Yii::$app->request->isAjax && $regModel->load(Yii::$app->request->post())) {
            if($regModel->validate()) {
                if($regModel->regUser()) {
                    $lib = new Library();
                    $pNumber = $lib->prettyPhoneNumber($regModel->phone);
                    Yii::$app->session->setFlash('success', Yii::t('app', 'We have sent a verification code to your {phone_number} via SMS. Enter the received code below.', ['phone_number'=>$pNumber]));
                    return $this->redirect(Url::to(['/profile/verify.html', 'msisdn'=>$pNumber, 'scope'=>'prime']));
                }
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($regModel);
            }
        }
    }

    public function actionLogin() {
        $model = new AuthForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $lib = new Library();
            $pNumber = $lib->prettyPhoneNumber($model->phone);
            $usr = Users::find()->where(['phone'=>$pNumber])->one();
            if($usr['status_id'] == 1) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'We have sent a verification code to your {phone_number} via SMS. Enter the received code below.', ['phone_number'=>$model->phone]));
                return $this->redirect(Url::to(['/profile/verify.html', 'msisdn'=>$pNumber]));
            } else {
                if ($model->login()) {
                    return $this->goBack();
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

}