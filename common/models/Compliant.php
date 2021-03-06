<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "compliant".
 *
 * @property integer $id
 * @property string $date_created
 * @property integer $from_user_id
 * @property integer $to_user_id
 * @property string $compliant_message
 * @property integer $satisfaction
 *
 * @property User $toUser
 * @property User $fromUser
 */
class Compliant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    const SATISFACTION_NO = 0;
    const SATISFACTION_YES = 1;
    
    /* syspect identity categories */
    const SI_FILTHY_LANGUAGE = 'filthy language';
    const SI_SEXUAL_HARASSMENT = 'sexual harassment';
    const SI_SPAM = 'spam';
    const SI_RACISM = 'racism';
    
    public $complains = array(
        self::SI_FILTHY_LANGUAGE,
        self::SI_SEXUAL_HARASSMENT,
        self::SI_RACISM,
        self::SI_SPAM
    );
    
    public static function tableName()
    {
        return 'compliant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_created'], 'safe'],
            [['from_user_id', 'to_user_id'], 'required'],
            [['from_user_id', 'to_user_id', 'satisfaction'], 'integer'],
            [['compliant_message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_created' => Yii::t('app', 'Date Created'),
            'from_user_id' => Yii::t('app', 'From User ID'),
            'to_user_id' => Yii::t('app', 'To User ID'),
            'compliant_message' => Yii::t('app', 'Compliant Message'),
            'satisfaction' => Yii::t('app', 'Satisfaction'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::className(), ['id' => 'from_user_id']);
    }
    /* get the model of users who has a 3 unsatisfact compliants */
    public function getSuspectsList(){
        $suspects = Compliant::find()
                ->innerJoinWith('toUser')
                ->onCondition(['satisfaction'=>self::SATISFACTION_NO])
                ->groupBy(['compliant.to_user_id'])
                ->having('COUNT(*) >= 3')
                ->all();
         return $suspects;
    }
}
