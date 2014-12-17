<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\widgets\Alert;
use kartik\datetime\DateTimePicker;
use kartik\widgets\FileInput;
?>
<!--<div class="content">-->
              
                       <div class="job-creator row">  
                <div class="left-column col-xs-12 col-sm-12 col-md-12 col-lg-7">
                    
                    <?php echo Html::beginForm(Url::to(['ticket/create'], TRUE), 'post', ['enctype'=>'multipart/form-data','class'=>'create-job with-background'])?>
                        <fieldset>
                            <?php  echo Alert::widget([
                                        'alertTypes' => ['error' => 'alert-danger'],
                                    ]);
                            ?>
                            <?php echo Html::label(Yii::t('app', 'Title'), 'title')?>
                            <?php echo Html::textInput('title', $model->title)?>
                            <?php echo Html::label(Yii::t('app', 'Choose Date and Time').':', 'finish_day')?>
                            <?php //echo Html::textInput('finish_day', $model->finish_day)?>
                            <?php echo DateTimePicker::widget([
                                'type' => DateTimePicker::TYPE_INPUT,
                                'name' => 'finish_day',
                                'value' => $model->finish_day,
                                'options' => ['format' =>'dd-M-yyyy hh:ii',
                                              'style' => 'display:inline',
                                             ],
                                'pluginOptions' => [
                                              'autoclose' => true
                                             ],
                            ])?>
                            <?php echo Html::label(Yii::t('app', 'Enter location').':', 'location') ?>
                            <?php echo Html::textInput('location', $model->location)?>
                            <div class="description">    
                                <?php echo Html::label(Yii::t('app', 'Description'.':'), 'description')?>
                                <?php echo Html::textarea('description', $model->description)?>
                                <a href="#" class="additional"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Make this information Private</a>
                            </div>
                            <label>Auto-translate to:</label>
                            <select id="translate">
                                <option>Russian</option>
                                <option>Russian</option>
                            </select>
                            <a href="#" class="btn btn-form">MAKE EDITABLE</a>
                            <textarea class="translation" disabled="disabled"></textarea> 
                            <div>
<!--                                <a href="#" class="btn btn-form left">ATTACH A PHOTO</a>-->
                                <?php
                                echo FileInput::widget([
                                    'name' => 'photo',
                                    'options' => [
                                        'multiple'=>false,
                                        ],
                                    'pluginOptions' => [
                                        'showPreview' => true,
                                        'showCaption' => false,
                                        'showRemove' => false,
                                        'showUpload' => false
                                    ],
                                ]);
                                ?>
                                <p class="attach-photo">Attach a photo to your description. Max file size: 5 Mb, Allowed extensions: JPG/PNG/GIF</p>                            
                                <div class="clearfix"></div>
                            </div>
                            <?php echo Html::label(Yii::t('app','Your price for this job').':', 'price')?>
                            <?php echo Html::textInput('price',$model->price, ['class'=>'yourprice']) ?>
                            <select name="currency" class="currency">
                                <option>USD</option>
                                <option>USD</option>
                            </select>
                            <a href="#" class="currency-price">See current prices for this of job</a>
                            <a href="#" class="btn btn-width">PUBLISH</a>                            
                        </fieldset>
                    <div id="addon1"></div>
                    <div id="addon2"></div>
                    <div id="addon3"></div>
                    <div id="addon4"></div>
                    <?php echo Html::submitButton()?>
                    <?php echo Html::endForm();?>

                                        
                </div>
                <div class="right-column choice-categories col-xs-12 col-sm-12 col-md-12 col-lg-5">
                    <p>Choose the categories for you job:</p>
                    <p class="commentary">You can choose up to 4 categories and 12 subcategories</p>
                    <ol class="select-categories">
                        <li class="number-in-order">
                        <select id="slot1"></select>
                        </li>
                        <li class="number-in-order">
                        <select id="slot2"></select>
                        </li>
                        <li class="number-in-order">
                        <select id='slot3'></select>
                        </li>
                        <li class="number-in-order">
                        <select id="slot4"></select>
                        </li>
                                                
                    </ol>
                <ol class="option-categories">
                    
                    <li class="sub-categiries" id="pnl1">
<!--                        <input type="checkbox"><label>1. Home &amp; Office repairs</label>  
                        <ul class="select-sub-categories">
                            <li><input type="checkbox"><label>painting</label></li>
                            <li><input type="checkbox"><label>furniture assembly</label></li>
                            <li><input type="checkbox"><label>plumbing</label></li>
                            <li><input type="checkbox"><label>electrical</label></li>
                            <li><input type="checkbox"><label>handyman</label></li>
                        </ul>
                    </li>-->
                    <li class="sub-categiries" id="pnl2"></li>
                     <li class="sub-categiries" id="pnl3"></li>
                    <li class="sub-categiries" id="pnl4"></li>
            </ol>
                   
                    
                    
               </div>     
                       </div>                                       
                   
                      
                   
  <!--</div>-->
  <div style="display:none;">
      <div id="slotsquantity"><?php echo Yii::$app->params['slots.quantity']?></div>
  <?php if(isset($categories)) { ?>
  <?php 
  $Items = NULL;
  foreach($categories as $cat_id => $category) { 
      $Items .=  '<div class="lvl1" style="display:block;font-weight:bold;" id='.$cat_id.'>'.$category['cat_name'];
      if(count($category) > 1){
          foreach($category as $i => $subcategory){
              if(is_string($i)) continue;
              $Items .= PHP_EOL.'<div class="lvl2" style="font-weight:normal;" id='.$subcategory['subcat_id'].'>'.$subcategory['subcat_name'].'</div>'.PHP_EOL;
          }
      }
      $Items .= '</div>'.PHP_EOL;
  } 
  ?>
  <?php echo $Items; ?>
  <?php } ?>
  </div>
<style>
    .lvl1{color:red;}
    .lvl2{color:navy;}
    </style>
    