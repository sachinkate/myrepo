<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Website\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Website\Entity\Websetting; 
use User\Entity\Signup; 
use Booking\Entity\Bookingadd;
use Booking\Entity\Bookingtraveller;
 use Booking\Entity\Bookingshare; 
 use User\Entity\UserAddress;
 use Booking\Entity\Bookingpayment;
use Website\Form\IndexForm;
use Website\Form\AboutusForm;
use Website\Form\ContactusForm;
use Website\Form\WebsettingForm;
use Doctrine\ORM\EntityManager;
 use Zend\Session\SessionManager;
use Zend\Session\Container;
use Signin\Entity\Signin;
use User\Entity\UserRoles; 
use Discounts\Entity\UserCouponLog;
use Doctrine\ORM\Query\Expr\Join;

use Admin\Smsservice\Smssender;

use Admin\Entity\Fmodeofpayment;
use Finance\Entity\Fcreditdebit;
use Booking\Entity\Bookingvendor;

//use Zend\Crypt\Password\Bcrypt;
//use Zend\Session\SessionManager;
//use Zend\Authentication\Storage\Session;
//use Zend\Session\Container;
 use Exception;
 
class IndexController extends AbstractActionController
{ 
       protected $em;
       protected $em2;
	   
		 	  protected    $PG_MERCHANT_KEY = "gtKFFx";   // Test 
  		     protected    $PG_SALT = "eCwWELxi";         // Test 
		      protected  $Uri_type="safegird";
                      protected  $test="test";
			  
//		    protected    $Uri_type="cabsaas";
//			protected    $PG_MERCHANT_KEY = "MeHnvY";  // Live
//  		    protected    $PG_SALT = "t02C93JB";   // Live
	   
    public function getEntityManager()
    {
         try 
        {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
        }
        catch(Exception $e)
        {
            echo "error";
        }
    } 
    
      public function getEntityManager2()
    {
        if (null === $this->em2) {
            $this->em2 = $this->getServiceLocator()->get('doctrine.entitymanager.orm_alternative');
        }
        return $this->em2;
    }  
    
    
    
       public function tandcAction()
    {
           $this->_checkSession();
           $this->getRealUrl();
$em = $this->getEntityManager();
         
               $tandc = $this->getEntityManager()->find('Website\Entity\Websetting', 1);
             
                return array(  
            'tandc'=>$tandc,
        );
        
         
     }
     public function aboutusAction()
    {
        $this->_checkSession();
         $this->getRealUrl();
$em = $this->getEntityManager();
         
               $aboutus = $this->getEntityManager()->find('Website\Entity\Websetting', 1);
             
                return array(  
            'aboutus'=>$aboutus,
        );
        
         
     }
//      public function contactusAction()
//    {
//          $this->_checkSession();
//          $this->getRealUrl();
//          $em = $this->getEntityManager();
//              $contactus = $this->getEntityManager()->find('Website\Entity\Websetting', 1);             
//                return array(  
//            'contactus'=>$contactus,
//        );
//          
//        
//        
//                 return array( 
//            'form'=>$form,
//        ); 
//         
//     }
        public function contactusAction(){
             $this->_checkSession();
          $this->getRealUrl();
           $em = $this->getEntityManager();
          $contactus = $this->getEntityManager()->find('Website\Entity\Websetting', 1); 
          $id = (int) $this->params()->fromRoute('id', 0);
          $mode = "edit";
          $form = new \Website\Form\CabEnquiryForm($em);
          if (!$id) {
                $cabEnquiry= new \Website\Entity\CabEnquiry($em);
                $mode = "add";
                $form->get('save')->setValue('Submit');  
          }
          else {
                $cabEnquiry = $this->getEntityManager()->find('Website\Entity\CabEnquiry', $id);
                $form->bind($cabEnquiry);
                $form->get('save')->setAttribute('value', 'Update'); 
            }
           $request = $this->getRequest();
           if ($request->isPost()) {
            $form->setInputFilter($cabEnquiry->getInputFilter());
            $form->setData($request->getPost());
         
            if ($form->isValid()) {
                 
               if($mode == "add")
                {
                 $cabEnquiry->exchangeArray($form->getData());
                 $this->getEntityManager()->persist($cabEnquiry);
                    $msg = 'Add Enquiry.';
                }
                else{
                    $msg = 'Edit Enquiry.';
                }
                $this->getEntityManager()->flush();
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'mode'=>$mode,
            'contactus'=>$contactus,
        );
            
    }
       public function privacypolicyAction()
    {
          $this->_checkSession();
          $this->getRealUrl();
          $em = $this->getEntityManager();
              $privacypolicy = $this->getEntityManager()->find('Website\Entity\Websetting', 1);             
                return array(  
            'privacypolicy'=>$privacypolicy,
        );
          
        
        
                 return array( 
            'form'=>$form,
        ); 
         
     }
       public function servicesAction()
    {
          $this->_checkSession();
          $this->getRealUrl();
          $em = $this->getEntityManager();
              $services = $this->getEntityManager()->find('Website\Entity\Websetting', 1);             
                return array(  
            'services'=>$services,
        );
          
        
        
                 return array( 
            'form'=>$form,
        ); 
         
     }
   public function websettingAction()
    {
        $this->_checkIfUserIsLoggedIn();
       $this->getRealUrl();
       $form = new WebsettingForm();
       
       $Websetting = $this->getEntityManager()->find('Website\Entity\Websetting', 1);
      
       if(!empty($Websetting))
       {
            $form->bind($Websetting);
            $Websetting = $this->getEntityManager()->find('Website\Entity\Websetting', 1);
       $form->get('submit')->setAttribute('value', 'Update'); 
           $request = $this->getRequest();
          if ($request->isPost()) {
            $form->setInputFilter($Websetting->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                
                    $msg = 'Component Percentage Updated Successfully.';
               
                $this->getEntityManager()->flush();
//                $this->FlashMessenger()
//                     ->setNamespace(\Zend\Mvc\Controller\Plugin\FlashMessenger::NAMESPACE_INFO)
//                     ->addMessage($msg);
     
            }
        }
   
       } 
       
 else {
  
                $request = $this->getRequest();
          if ($request->isPost()) {
              $Websetting = new Websetting();
            $form->setInputFilter($Websetting->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                
                 $Websetting->exchangeArray($form->getData());
                $this->getEntityManager()->persist($Websetting);
                $this->getEntityManager()->flush();
                    $msg = 'Component Percentage Updated Successfully.';
               
                
//                $this->FlashMessenger()
//                     ->setNamespace(\Zend\Mvc\Controller\Plugin\FlashMessenger::NAMESPACE_INFO)
//                     ->addMessage($msg);
     
            }
        }

        }

       
       
       
        return array( 
            'form'=>$form,
        ); 
   }  
    
    public function indexAction()
    {
         $this->_checkSession();
         $this->getRealUrl();
         $loginenv=$this->getenv();
       //  print_r($loginenv);die;
        $companyname=$this->returnCompanynamelogo();
        $request = $this->getRequest();
        $em = $this->getEntityManager();
        $form = new IndexForm($em);
              
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->add('select', 'tl')
              ->add('from', '\Website\Entity\TarriffList tl')
                ->where('tl.status=1');         
        $tl = $queryBuilder->getQuery()->getArrayResult();
        
        $queryBuilder->add('select', 'tsl')
              ->add('from', '\Admin\Entity\TarriffSubList tsl')
                ->where('tsl.status=1');         
        $tsl = $queryBuilder->getQuery()->getArrayResult();
        

          $css='websitestyle';   
        
      // echo $this->companyname;die;
        
        return array('form' => $form,'TarriffList'=> $tl, 'TarriffSubList'=> $tsl,'scc' => $this->flashMessenger()->getCurrentSuccessMessages(), 'err' => $this->flashMessenger()->getCurrentErrorMessages(),'css'=>$css,'companyname'=>$companyname);
        
    }
     public function getRealUrl()
    {
         $loginenv=$this->getenv();
        $hosturl=$_SERVER['HTTP_HOST'];
         $hostarry = explode('.', $hosturl);
          if($hostarry[1]==$loginenv)
          {
            $em2=$this->getEntityManager2();
            $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
                if ($loginUrl === null)
                    {
                        if($loginenv=="cabsaas")
                        {
                            $this->plugin('redirect')->toUrl("https://www.".$loginenv.".com/registration");
                        }
                        else
                        {
                            $this->plugin('redirect')->toUrl("http://www.".$loginenv.".com/registration");
                        }
                        return FALSE;
                        //return $this->redirect()->toRoute('registration',array('controller'=>'registration'));
                    }
                else
                    {
                        $finalurl=$hosturl;
                    }
          }
          else
          {
            $result = dns_get_record($hosturl);
            for($r=0;$r<count($result);$r++)
            {
               $loginurlval=$result[$r]['target'];
                $em = $this->getEntityManager();
            $success = $em->getRepository('Registration\Entity\Registration')->findOneByloginUrl($loginurlval);
            if ($success !== null) 
                {
                      $finalurl=$loginurlval;
                }
            else
                {
                    $status=1;
                }
            }
            if($status==1)
            {
                $redirectUrl='http://www.'.$loginenv.'.com/registration';
				return $this->redirect()->toUrl($redirectUrl);
            }
          }
        $hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        if($companyentity->logo!="")
        {
            $userCompanyLogo=$companyentity->logo;
        }
        else
        {
            $userCompanyLogo="logo.jpg";
        }
        $logopath='/images/company/clogo/'.$userCompanyLogo;
         if($loginenv=="cabsaas")
                        {
                                $loginurllogo = "https://" . $loginurl1.$logopath;
                        }
                        else
                        {
                             $loginurllogo = "http://" . $loginurl1.$logopath;
                        }
        $companynameVal=$userCompanyName;
       // echo $loginurllogo; echo $userCompanyName; die;
         $this->layout()->setVariable('logopath', $loginurllogo);
         $this->layout()->setVariable('companyname',$userCompanyName);
         return $hosturl;
    }
       public function returnCompanynamelogo()
    {
           $loginenv=$this->getenv();
        $hosturl=$_SERVER['HTTP_HOST'];
         $hostarry = explode('.', $hosturl);
          if($hostarry[1]==$loginenv)
          {
            $em2=$this->getEntityManager2();
            $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
                if ($loginUrl === null)
                    {
                     if($loginenv=="cabsaas")
                        {
                        $this->plugin('redirect')->toUrl("https://www.".$loginenv.".com/registration");
                        return FALSE;
                        }
                        else
                        {
                             $this->plugin('redirect')->toUrl("http://www.".$loginenv.".com/registration");
                        return FALSE;
                        }
                        //return $this->redirect()->toRoute('registration',array('controller'=>'registration'));
                    }
                else
                    {
                        $finalurl=$hosturl;
                    }
          }
          else
          {
            $result = dns_get_record($hosturl);
            for($r=0;$r<count($result);$r++)
            {
               $loginurlval=$result[$r]['target'];
                $em = $this->getEntityManager();
            $success = $em->getRepository('Registration\Entity\Registration')->findOneByloginUrl($loginurlval);
            if ($success !== null) 
                {
                      $finalurl=$loginurlval;
                }
            else
                {
                    $status=1;
                }
            }
            if($status==1)
            {
                $redirectUrl='http://www.'.$loginenv.'.com/registration';
                                                            return $this->redirect()->toUrl($redirectUrl);
            }
          }
        $hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        if($companyentity->logo!="")
        {
            $userCompanyLogo=$companyentity->logo;
        }
        else
        {
            $userCompanyLogo="logo.jpg";
        }
        $logopath='/images/company/clogo/'.$userCompanyLogo;
         if($loginenv=="cabsaas")
                        {
        $loginurllogo = "https://" . $loginurl1.$logopath;
                        }
                        else
                        {
                            $loginurllogo = "http://" . $loginurl1.$logopath;
                        }
        return $userCompanyName;
       // echo $loginurllogo; echo $userCompanyName; die;
         
    }
    public function searchAction()
    { 
        $loginenv=$this->getenv();
         $this->_checkSession();
        $this->getRealUrl();
          ////////for discount ///
        $session = new Container('Discount');
 $session->BasicAmount=0;
  $session->ServiceTax=0;
  $session->StateCharge=0;
 $session->TotalAmount=0;
 $session->Advance=0;
 $session->Balance=0;
 $session->Paying=0;
 $session->seat=1;
 $session->type=0;
 $sessionp=new Container('Before')   ;
   $sessiont =new Container('Triggered');
   $sessiont->triggered=0;
//$servicetax//=$sessionp->postmode;
$sessionp->postbasic=0;
$sessionp->postst=0;
$sessionp->postsc=0;
$sessionp->postad=0;
$sessionp->postbal=0;
$session->offsetUnset('Amountpay'); 
        /////////end//
           if (empty($_POST)) {
            return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'index'
                ));
         
        }
        
        
            $bsession = new \Zend\Session\Container('Booking');
          
         
         $em = $this->getEntityManager();
         
          $form = new IndexForm($em);
           $form->get('submit')->setAttribute('value', 'Modify Search');
           $request = $this->getRequest();
            if ($request->isPost()) {
             
           $_POST =   $request->getPost()->toArray();
           
                $seconds_diff = strtotime($_POST['tEndDate']) - strtotime($_POST['tDate']);
         
         $minutes= $seconds_diff/60;
         $hours= $minutes/60;
         $noOfDays =$hours/24;
       
         
         
        	 if (array_key_exists('typeoftrip',$_POST))
			  {
				"Key exists!";
			  }
			else
			  {
				$_POST['typeoftrip']='0-6';
			  }
					  
         
         
         if($_POST['typeoftrip']=='3-1')
           {
             if(!empty($_POST['copydcity']))
             {
            $copydcity = implode(',', $_POST['copydcity']);
             }
           else {$copydcity=''; }
         }
           else {$copydcity=''; }
         
           $uid=0;
           // $serviceTax = '5.6'; 
            $serviceTax = '0'; 
             $noOfDays=$noOfDays+1;
              $noOfDays= floor($noOfDays); 
            $mainarraygettripvalues=array(); 
             $scityid=   $_POST['pCity'];
             $dcityid=  $_POST['dCity'];
              $Outstationtypeid =  $this->getapproximatedistance($scityid,$dcityid);
              if(!empty($Outstationtypeid))
              {
      $outstationid = $Outstationtypeid[0]['outstationTypeId'];
       $approx = $Outstationtypeid[0]['distance'];
              } 
           ///////*outsation/roundtrip start*///
           if($_POST['typeoftrip']=='1-1' && (!empty($Outstationtypeid)) )
           {  //echo "os/rt";die;
            
                $getroundtripvalues = $this->getroundtripvalues($scityid,$dcityid, $uid);
           // print_r($getroundtripvalues);
            
             for($i=0; $i<count($getroundtripvalues);$i++) 
         {
         $hillcharges =  $this->gethillcharges($outstationid,$getroundtripvalues[$i]['vehicleId']);
         ///////////get hill charges /get multipale value////
                  
         if(!empty($hillcharges))
          {           
        $getroundtripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getroundtripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getroundtripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getroundtripvalues[$i]['hillCharge']= 0  ;
        $getroundtripvalues[$i]['stateCharge']= 0  ;
        $getroundtripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getroundtripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        
     
        //////////fare calculation///////
           $maxKm = 0;
          
            $minAvg = $mainarraygettripvalues[$i]['MinimumChargedDistance'] * $noOfDays;
                    if ($approx > $minAvg) {

                        $maxKm = $approx;
                    }
                    else {
                        $maxKm = $minAvg;
                    } 
           
           $basicFare = ($maxKm * $mainarraygettripvalues[$i]['perKm']);
             
                    $driverAllowance = $mainarraygettripvalues[$i]['driverCharges'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt'] * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge']   + $mainarraygettripvalues[$i]['hillCharge'];
                      $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges)  ;
                      //$apidiscount =round(($totalCharges * 5) / 100);
                      //$totalCharges = $totalCharges-$apidiscount;
                     // $serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                   //  $totalFare =  round($totalCharges + $serviceTaxApllied);
                      $totalFare =  round($totalCharges );
                                         
                       if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }                  
              // $mainarraygettripvalues[$i]['ApiDiscount'] = $apidiscount;
           //unset($mainarraygettripvalues[$i]['vehicleId']);     
         
    }
              // print_r($mainarraygettripvalues);            
    
           }
           ///////*outsation/roundtrip end*///
           
           
           
            /*outsation/oneway start*/
        
    if($_POST['typeoftrip']=='2-1' && (!empty($Outstationtypeid)))
       {
    
   ///////////get getonewaytripvalues  //////////////
       
    $getonewaytripvalues = $this->getonewayvalues($scityid,$dcityid,$uid);
   
     ///////////endget getonewaytripvalues  //////////////
    
     for($i=0; $i<count($getonewaytripvalues);$i++)
    {
         $hillcharges =  $this->gethillcharges($outstationid,$getonewaytripvalues[$i]['vehicleId']);
         ///////////get hill charges /get multipale value////
                  
         if(!empty($hillcharges))
          {           
        $getonewaytripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getonewaytripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getonewaytripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getonewaytripvalues[$i]['hillCharge']= 0  ;
        $getonewaytripvalues[$i]['stateCharge']= 0  ;
        $getonewaytripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getonewaytripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        //$mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
     
        //////////fare calculation///////
            $maxKm = 0;
                $minAvg =  $mainarraygettripvalues[$i]['minAvgPerDay'] *  $noOfDays;
                    if ( $approx >  $minAvg) {

                         $maxKm =  $approx;
                    }
                    else {
                         $maxKm =  $minAvg;
                    }
                    
                     $maxKm = ( $maxKm / 2);
// DO NOT DELETE IMP FOR FULL CALCULATION WITH NIGHT HALT AND CHARGES        
//var tot=((maxKm*perKmRate)+(maxKm*oneWayPerKmRate)+drivAllPerDay+nightHalt+otherCharges+hillcharges+stateCharges)*noOfCars*noOfDays;


                     $basicFare = (( $maxKm *  $mainarraygettripvalues[$i]['perKm']) + ( $maxKm *  $mainarraygettripvalues[$i]['oneWayPerKmRate'] ));
                     $driverAllowance = $mainarraygettripvalues[$i]['drivAllPerDay'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt']   * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge']  + $mainarraygettripvalues[$i]['hillCharge'] ;
                     $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges)  ;
                     //$apidiscount =round(($totalCharges * 5) / 100);
                     // $totalCharges = $totalCharges-$apidiscount;
                    // $serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                     $totalFare =  round($totalCharges );


//var tot = ((maxKm * perKmRate) + (maxKm * oneWayPerKmRate) + drivAllPerDay + otherCharges + hillcharges + stateCharges) * noOfCars * noOfDays;
                    if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }                  
               
          // unset($mainarraygettripvalues[$i]['vehicleId']);   
                        
         
    }
    
     // print_r($getonewaytripvalues); 
     // print_r($mainarraygettripvalues);die;
    
       }
    /*outsation/oneway end*/ 
           
           
         ////outsation/multicity start//
        if($_POST['typeoftrip']=='3-1')
       {
            
         //  Array ( [typeoftrip] => 3-1 [pCity] => 8 [dCity] => 25 [copydcity] => Array ( [0] => 8 [1] => 25 [2] => 8 ) [tDate] => 2015-10-26 [pickupTime] => 1:30 am [tEndDate] => 2015-10-27 [endTime] => 1:30 am [submit] => Add booking ) 
            $dcityidmainarr = array();
            ///get city id by name//////
             //$scityid = $this->getcityid($scityname);
             $scityid = $_POST['pCity'];
             $dCityArr= array();
               array_push($dCityArr, $_POST['dCity']);  
               
               if(!empty($_POST['copydcity']))
               {
                  for ($i = 0; $i < sizeof($_POST['copydcity']); $i++)
             {
             
                  array_push($dCityArr, $_POST['copydcity'][$i]); 
                 
                    
             }
               }
 
           
              
         $mainarraygettripvalues=    $this->getmulticityapproximatedistance($scityid,$dCityArr,$noOfDays);
          // print_r($mainarraygettripvalues); die;
        //  echo "<br>";echo "<br>";echo "<br>";echo "<br>";
                  if(!empty($mainarraygettripvalues))
                  {
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
              $maxKm = 0;
           
            $minAvg = $mainarraygettripvalues[$i]['MinimumChargedDistance'] * $noOfDays;
                    if ($mainarraygettripvalues[$i]['ApproxDistance'] > $minAvg) {

                        $maxKm = $mainarraygettripvalues[$i]['ApproxDistance'];
                    }
                    else {
                        $maxKm = $minAvg;
                    } 
           
           $basicFare = ($maxKm * $mainarraygettripvalues[$i]['perKm']);
             
                    $driverAllowance = $mainarraygettripvalues[$i]['driverCharges'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt'] * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge'] +  $mainarraygettripvalues[$i]['hillCharge'];
                      $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges)  ;
                   // $apidiscount =round(($totalCharges * 5) / 100);
                    //  $totalCharges = $totalCharges-$apidiscount;
                      //$serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                     $totalFare =  round($totalCharges );
                                         
                       if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }   
                       //unset($mainarraygettripvalues[$i]['vehicleId']);
                          //$mainarraygettripvalues[$i]['destinationCity']=$dcityname;
         }
                  }
        // print_r($mainarraygettripvalues    );die; 
        }        
        ////outsation/multicity end//  
           
           
            ////local/fulday start//
         if($_POST['typeoftrip']=='4-2')
         {
             
              
         $mainarraygettripvalues=     $this->getfulldayvalues($scityid,$uid);
         
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
            $tot = ($mainarraygettripvalues[$i]['localBasicRate'] )   * $noOfDays;
                // $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                  // $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                 // unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
                  // print_r($mainarraygettripvalues);die;
        }
         
         ////local/fulday end//
           
          ////local/halfday start//
         if($_POST['typeoftrip']=='5-2')
         {
             
              
         $mainarraygettripvalues=     $this->gethalfdayvalues($scityid,$uid);
          
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
            $tot = ($mainarraygettripvalues[$i]['localBasicRate'] )   * $noOfDays;
                //  $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                 //  $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                 // unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
                   // print_r($mainarraygettripvalues);die;
        }
         
         ////local/halfday end//
        
        
         //transfer/airport to location start///
        if($_POST['typeoftrip']=='6-3'|| $_POST['typeoftrip']=='7-3' || $_POST['typeoftrip']=='8-3')
        {
         
          $scityid=   $_POST['plCity'];
          $plocid =  $_POST['plfromCity'];
          $dlocid =  $_POST['pltoCity'];
          ///get location id//
         
        
           $ctid = $scityid;          
        ////get location id//////
           
           //get distance///
           
            if($_POST['typeoftrip']=='8-3')
           {
           $distance = $this->getdistance($dlocid,$plocid,$ctid);
           }
 else {$distance = $this->getdistance($plocid,$dlocid,$ctid);}
           
           
          
              $locDistance = $distance[0]['locDistance']; 
           //get distance///
          
         //get transfer rate///
       $mainarraygettripvalues =  $this->gettransferrate($ctid,$uid);
      // print_r($mainarraygettripvalues);  die;
       //////fare calculation start//
               for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
                //   $mainarraygettripvalues[$i]['sourceCity'] = $scityname;
                   if($locDistance <= 10)
                   {
                       $tot = ($mainarraygettripvalues[$i]['FareUpto10km'] )   * $noOfDays;
                      unset($mainarraygettripvalues[$i]['FareUpto20km']);
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                      $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto10km'];
                     unset($mainarraygettripvalues[$i]['FareUpto10km']); 
                   }
                   if($locDistance >10 && $locDistance <=20)
                   {
                       $tot = ($mainarraygettripvalues[$i]['FareUpto20km'] )   * $noOfDays;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']); 
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto20km'];
                     unset($mainarraygettripvalues[$i]['FareUpto20km']);  
                      }
                   if($locDistance >20 && $locDistance <=40)
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto40km'] )   * $noOfDays;
                        unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto40km'];
                     unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      }
                   if($locDistance >40 && $locDistance <=60)                       
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto60km'] )   * $noOfDays;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto60km'];
                     unset($mainarraygettripvalues[$i]['FareUpto60km']);  
                      }
                   if($locDistance >60 && $locDistance <=80)
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto80km'] )   * $noOfDays;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                   
                      $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto80km'];
                     unset($mainarraygettripvalues[$i]['FareUpto80km']);
                   }
                   if($locDistance >80  )
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareAbove100km'] )   * $noOfDays;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareAbove100km'];
                     unset($mainarraygettripvalues[$i]['FareAbove100km']);  
                      }
                //    $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                 //  $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                  //unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
        //////fare calculation end///
       ////get transfer rate///
         // print_r($mainarraygettripvalues);
           //die;
        }        
//transfer/airport to location end///
        
           //////cab sharing///////
        
        if( $_POST['typeoftrip']=='0-6')
        {
             
         $getcabsetfarevalues=array();
         
    $getcabvalues = $this->getcab($scityid,$dcityid,$_POST['tDate']);
         
         for($i=0;$i<count($getcabvalues);$i++)
         {
             $getcabsetfarevalues = $this->getseatfare($getcabvalues[$i][0]['vShId'],$_POST['tDate'],$getcabvalues[$i][0]['slotId']);
             
                if(!empty($getcabsetfarevalues)) 
                {
             for($j=0;$j<count($getcabsetfarevalues);$j++)
             {
                  
             array_push($getcabvalues[$i], $getcabsetfarevalues[$j]);
             }
                }
                 
            
         }
          // print_r($getcabvalues);die;
          static $count  ;
            $count = count($getcabvalues);
            for($del=0;$del< $count;$del++)
            {
                 
            if(count($getcabvalues[$del])==7)
           {
                unset( $getcabvalues[$del] );
                     $getcabvalues = array_values($getcabvalues); 
                     $count = count($getcabvalues);
                     $del--;
                      //print_r($getcabvalues);die;
           }
            }
          
           
         $getcabvalues = array_values($getcabvalues); 
      
         $mainarraygettripvalues = $getcabvalues;
		 
         
        //   print_r($mainarraygettripvalues);///////code for no seat left///
 //        static $maincount;
//         $maincount = count($mainarraygettripvalues);
//          for($m=0;$m<$maincount;$m++) 
//			 { 
//					 $remainseat=''; 
//				   $seats =  $mainarraygettripvalues[$m]['vehicleSeatCapacity'];
//						  for($j=1;$j<=($seats);$j++)
//								   {
//							 // implode($remainseat, $pieces)lode
//							  $remainseat .=$mainarraygettripvalues[$m][$j]['status'] ;
//								 
//								   }
//									   $leftseats = substr_count($remainseat, 0)+substr_count($remainseat, 3);
//								   if($leftseats==0)
//								   {
//								   unset( $mainarraygettripvalues[$m] );
//								 $mainarraygettripvalues = array_values($mainarraygettripvalues); 
//								 $maincount = count($mainarraygettripvalues);
//								 $m--;
//								   }
//			 } 
 			 
			 ///////update current session for free seat processs/////
 
//   $statusChk = $this->getEntityManager()->getRepository('\Tariff\Entity\Shared')->findOneBy(array('processtime'=>$Vsid,'status'=>3,'tdate'=>$_POST['tDate']) );
 
 
   if( $_POST['typeoftrip']=='0-6')  ////////for cab shere/////
        {
			$cdd =date('Y-m-d h:i:s');//die;2015-10-30 06:47:08
			//$date = date("Y-m-d H:i:s");
			$time = strtotime($cdd);
			$time = $time - (10 * 60); 
			$minustime = date("Y-m-d H:i:s", $time);
			$queryBuilder = $em->createQueryBuilder();
			$queryBuilder->update('\Tariff\Entity\Shared sh')
			->set('sh.currentsession', $queryBuilder->expr()->literal(''))
			->set('sh.status', $queryBuilder->expr()->literal('0'))
			//->set('sh.tdate', $queryBuilder->expr()->literal($_POST['tDate']))
			->where("sh.processtime < '".$minustime."' AND sh.tdate= '".$_POST['tDate']."' AND sh.status=3");    
			$ress = $queryBuilder->getQuery()->getArrayResult();
			
			  $bsession = new \Zend\Session\Container('Booking' );
			 
			  $curSes=$bsession->getManager()->getId(); 
			  
			$queryBuilder = $em->createQueryBuilder();
			$queryBuilder->update('\Tariff\Entity\Shared sh')
			->set('sh.currentsession', $queryBuilder->expr()->literal(''))
			->set('sh.status', $queryBuilder->expr()->literal('0')) 
			->where("sh.currentsession = '".$curSes."' AND sh.tdate= '".$_POST['tDate']."' AND sh.status=3");    
			$ress = $queryBuilder->getQuery()->getArrayResult();
			
		}
		
 //print_r($tlss);die;
 ////////update current session for free seat processs//////
			 
			 
			 
         //print_r($mainarraygettripvalues);
     ///////////   //////////////
        }
        
		
		if( $_POST['typeoftrip']=='0-7') ////////for Deal/////
        {  
			$cdd =date('Y-m-d h:i:s');//die;2015-10-30 06:47:08
			//$date = date("Y-m-d H:i:s");
			  $time = strtotime($cdd);
			  $time = $time - (10 * 60); 
			  $minustime = date("Y-m-d H:i:s", $time);
			$queryBuilder = $em->createQueryBuilder();
			$queryBuilder->update('\Tariff\Entity\Deals d')
			->set('d.currentsession', $queryBuilder->expr()->literal(''))
			->set('d.status', $queryBuilder->expr()->literal('0'))
			//->set('sh.tdate', $queryBuilder->expr()->literal($_POST['tDate']))
			->where("d.processtime < '".$minustime."' AND d.tDate= '".$_POST['tDate']."' AND d.status=2");    
			$ress = $queryBuilder->getQuery()->getArrayResult();
			
			  $bsession = new \Zend\Session\Container('Booking' );
			 
			  $curSes=$bsession->getManager()->getId(); 
			  
			$queryBuilder = $em->createQueryBuilder();
			$queryBuilder->update('\Tariff\Entity\Deals dl')
			->set('dl.currentsession', $queryBuilder->expr()->literal(''))
			->set('dl.status', $queryBuilder->expr()->literal('0')) 
			->where("dl.currentsession = '".$curSes."' AND dl.tDate= '".$_POST['tDate']."' AND dl.status=2");    
			$ress = $queryBuilder->getQuery()->getArrayResult();
			
		}
		
        //////////cab sharing///////
		
		
		/////// Deal /////
		
		if($_POST['typeoftrip']=='0-7'){
             $deals=$this->getdeals($scityid,$dcityid,$_POST['tDate']);
             $mainarraygettripvalues=$deals;
            }
			
		
		/////// Deal /////
			
            }
            
           $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->add('select', 'tl')
              ->add('from', '\Website\Entity\TarriffList tl')
                ->where('tl.status=1');         
        $tl = $queryBuilder->getQuery()->getArrayResult();
        
        $queryBuilder->add('select', 'tsl')
              ->add('from', '\Admin\Entity\TarriffSubList tsl')
                ->where('tsl.status=1');         
        $tsl = $queryBuilder->getQuery()->getArrayResult();
        
        
//        $queryBuilder->add('select', 'css')
//              ->add('from', '\Website\Entity\Css css')
//                ->where('css.status=1');         
//        $css = $queryBuilder->getQuery()->getArrayResult();
//        
//        if(!empty($css))
//        $css=$css[0]['css'];
//        else
          $css='websitestyle';   
             
            
            
            
           return array('form' => $form,'postarr' =>  $_POST ,'TarriffList'=> $tl, 'TarriffSubList'=> $tsl,'mainarraygettripvalues'=>$mainarraygettripvalues,'copydcity'=>$copydcity,'Outstationtypeid'=>$Outstationtypeid,'noOfDays'=>$noOfDays,'css'=>$css,'currentsession'=>$bsession->getManager()->getId());  
        
       
     
    }
    ///////// Deals ///////
    
     public function godealAction() {
   $loginenv=$this->getenv();
             $dealId = filter_input(INPUT_POST, 'deal');
            
             $Trip = filter_input(INPUT_POST, 'sharetrip');
             $droptext = filter_input(INPUT_POST, 'droptext');
             $pickuptext = filter_input(INPUT_POST, 'pickuptext');                
             $drop = filter_input(INPUT_POST, 'drop');
             $pickup = filter_input(INPUT_POST, 'pickup');
             $bsession = new \Zend\Session\Container('Booking' );
             $bsession->dealId = $dealId; $bsession->Trip = $Trip;$bsession->droptext = $droptext;$bsession->pickuptext = $pickuptext;
             $bsession->drop = $drop;$bsession->pickup = $pickup;
            $result = true;
         echo json_encode($result);
       exit; 
    }
     function getdealDetails($dealId)
       {
         $loginenv=$this->getenv();
           $em = $this->getEntityManager();
           $queryBuilder = $em->createQueryBuilder();
    
                 $queryBuilder->add('select', 'dls.dId as dealId,dls.sCityId as scid,cs.ctname as SourceCity,dls.dCityId as dstctId ,cd.ctname as DestinationCity,dls.pickLocId as srcPickupLoc,ls.locname as srcLocName,dls.dropLocId as drpLocId,ld.locname as drpLocName,dls.distance,dls.vehicleId,v.vehicleName,dls.slotIdFrom as frmSlotId,tss.time as frmtime,dls.slotIdTo as toSlotId,tsd.time as toTime ,dls.extKm,dls.comment,dls.basicFare,dls.dealFare,dls.tDate')
                    ->add('from', '\Tariff\Entity\Deals dls')
                    ->leftJoin('\Admin\Entity\City', 'cs', \Doctrine\ORM\Query\Expr\Join::WITH, 'cs.cityId=dls.sCityId')
                    ->leftJoin('\Admin\Entity\City', 'cd', \Doctrine\ORM\Query\Expr\Join::WITH, 'cd.cityId=dls.dCityId')     
                    ->leftJoin('\Admin\Entity\Location', 'ls', \Doctrine\ORM\Query\Expr\Join::WITH, 'ls.locationId=dls.pickLocId')          
                    ->leftJoin('\Admin\Entity\Location', 'ld', \Doctrine\ORM\Query\Expr\Join::WITH, 'ld.locationId=dls.dropLocId')               
                    ->leftJoin('\Admin\Entity\Vehicle', 'v', \Doctrine\ORM\Query\Expr\Join::WITH, 'v.vehicleId=dls.vehicleId')                    
                    ->leftJoin('\Admin\Entity\TimeSlot', 'tss', \Doctrine\ORM\Query\Expr\Join::WITH, 'tss.slotId=dls.slotIdFrom')                          
                    ->leftJoin('\Admin\Entity\TimeSlot', 'tsd', \Doctrine\ORM\Query\Expr\Join::WITH, 'tsd.slotId=dls.slotIdTo')                                
                    ->where("dls.dId =  '" . $dealId . "'") ;     
          $result = $queryBuilder->getQuery()->getArrayResult();
		    
         return $result;
           
       }
    function getdeals($pcity,$dcity,$tDate)
       {
        $loginenv=$this->getenv();
           $em = $this->getEntityManager();
           $queryBuilder = $em->createQueryBuilder();
    
                 $queryBuilder->add('select', 'dls.dId as dealId,dls.sCityId as scid,cs.ctname as scname,dls.dCityId as dstctId ,cd.ctname as dstctname,dls.pickLocId as srcPickupLoc,ls.locname as srcLocName,dls.dropLocId as drpLocId,ld.locname as drpLocName,dls.distance,dls.vehicleId,v.vehicleName,v.vPhoto,dls.slotIdFrom as frmSlotId,tss.time as frmtime,dls.slotIdTo as toSlotId,tsd.time as toTime ,dls.extKm,dls.comment,dls.basicFare,dls.dealFare,dls.tDate,dls.status')
                    ->add('from', '\Tariff\Entity\Deals dls')
                    ->leftJoin('\Admin\Entity\City', 'cs', \Doctrine\ORM\Query\Expr\Join::WITH, 'cs.cityId=dls.sCityId')
                    ->leftJoin('\Admin\Entity\City', 'cd', \Doctrine\ORM\Query\Expr\Join::WITH, 'cd.cityId=dls.dCityId')     
                    ->leftJoin('\Admin\Entity\Location', 'ls', \Doctrine\ORM\Query\Expr\Join::WITH, 'ls.locationId=dls.pickLocId')          
                    ->leftJoin('\Admin\Entity\Location', 'ld', \Doctrine\ORM\Query\Expr\Join::WITH, 'ld.locationId=dls.dropLocId')               
                    ->leftJoin('\Admin\Entity\Vehicle', 'v', \Doctrine\ORM\Query\Expr\Join::WITH, 'v.vehicleId=dls.vehicleId')                    
                    ->leftJoin('\Admin\Entity\TimeSlot', 'tss', \Doctrine\ORM\Query\Expr\Join::WITH, 'tss.slotId=dls.slotIdFrom')                          
                    ->leftJoin('\Admin\Entity\TimeSlot', 'tsd', \Doctrine\ORM\Query\Expr\Join::WITH, 'tsd.slotId=dls.slotIdTo')
					//->innerJoin('\Booking\Entity\Bookingshare', 'bksh',\Doctrine\ORM\Query\Expr\Join::WITH,
           // 'bksh.seatNo != dls.dId ')
                    ->where("dls.sCityId =  '" . $pcity . "' AND  dls.dCityId ='" . $dcity . "' AND dls.tDate='".$tDate."' AND dls.status IN (0,1,2)  ") ;     
          $result = $queryBuilder->getQuery()->getArrayResult();
       
        return $result;
           
       }
	   
    function getdealpicdroplocAction() {
        $loginenv=$this->getenv();
       $dealId = filter_input(INPUT_POST, 'id');
         $em = $this->getEntityManager();
         $queryBuilder = $em->createQueryBuilder(); 
         $queryBuilder->add('select', 'pdloc1,pkloc1.locname as locationName')
                    ->add('from', '\Tariff\Entity\Pickdroplocdeal pdloc1')
                    ->leftJoin('\Admin\Entity\Location', 'pkloc1',\Doctrine\ORM\Query\Expr\Join::WITH,
                        'pkloc1.locationId= pdloc1.locId')
                    ->where("pdloc1.dId =  '" . $dealId . "' and  pdloc1.status =  0 ");
        $picresult = $queryBuilder->getQuery()->getArrayResult();
        $queryBuilder->add('select', 'pdloc, drloc.locname as locationName')
                    ->add('from', '\Tariff\Entity\Pickdroplocdeal pdloc')
                    ->leftJoin('\Admin\Entity\Location', 'drloc',\Doctrine\ORM\Query\Expr\Join::WITH,
            'drloc.locationId= pdloc.locId ')
                ->where("pdloc.dId =  '" . $dealId . "' and  pdloc.status =1 ");
       $dropresult = $queryBuilder->getQuery()->getArrayResult();
     
        $result = array( 'picresult' => $picresult, 'dropresult' => $dropresult);
      
        echo json_encode($result);
        exit;
    }
    
    ///////// Deals ///////
      public function gobookingAction()
    {
           $loginenv=$this->getenv();
          
           $Vid = filter_input(INPUT_POST, 'Vid');
            $Trip = filter_input(INPUT_POST, 'Trip');
             $Tdate = filter_input(INPUT_POST, 'Tdate');
            $TEdate = filter_input(INPUT_POST, 'TEdate');
             $STtime = filter_input(INPUT_POST, 'STtime');
            $ETtime = filter_input(INPUT_POST, 'ETtime');
            $PCity = filter_input(INPUT_POST, 'PCity');
            $DCity = filter_input(INPUT_POST, 'DCity');
            $Cars = filter_input(INPUT_POST, 'Cars');
            $Pmode = filter_input(INPUT_POST, 'Pmode');
            $noofdays = filter_input(INPUT_POST, 'noofdays');
            $PCityshow = filter_input(INPUT_POST, 'PCityshow');
            $DCityshow = filter_input(INPUT_POST, 'DCityshow');
            $copydcity = filter_input(INPUT_POST, 'copydcity');            
            $plcity = filter_input(INPUT_POST, 'plcity');
            $plfromCity = filter_input(INPUT_POST, 'plfromCity');
            $pltoCity = filter_input(INPUT_POST, 'pltoCity');
            
             $plCityval = filter_input(INPUT_POST, 'plCityval');
            $plfromCityval = filter_input(INPUT_POST, 'plfromCityval');
            $pltoCityval = filter_input(INPUT_POST, 'pltoCityval');
            
            
       
           $bsession = new \Zend\Session\Container('Booking');
           $bsession->vid = $Vid; $bsession->Trip = $Trip; $bsession->Tdate = $Tdate; $bsession->TEdate = $TEdate; $bsession->STtime = $STtime; $bsession->ETtime = $ETtime; $bsession->PCity = $PCity; $bsession->DCity = $DCity; $bsession->Cars = $Cars;$bsession->Pmode = $Pmode; $bsession->noofdays = $noofdays; $bsession->PCityshow = $PCityshow; $bsession->DCityshow = $DCityshow;$bsession->copydcity = $copydcity;$bsession->plcity = $plcity;$bsession->plfromCity = $plfromCity;$bsession->pltoCity = $pltoCity;$bsession->plCityval = $plCityval;$bsession->plfromCityval = $plfromCityval;$bsession->pltoCityval = $pltoCityval;
       
          
       $result = true;
         echo json_encode($result);
       exit;  
    }
    
      public function bookingAction()
    {  
          $loginenv=$this->getenv();
           $this->_checkSession();
          $this->getRealUrl();
        $bsession = new \Zend\Session\Container('Booking');  
        
         
      $dealId= $bsession->dealId;
	  
	  
	   ///////////// Get Login user data Start 
		 if($this->_checkSession())
			 {
				$userarr1 = $this->_userIdentityValue();
				$firstName = $userarr1->firstName;
				$lastName = $userarr1->lastName;
				$umobile = $userarr1->mobile;
				$email = $userarr1->email;
 				$subuserid = $userarr1->subuserid;
				$bsession->subuserid=$subuserid;
                                
			    ///////get offline payment mode/////
				$em = $this->getEntityManager();
				$queryoffmodewpay = $em->createQueryBuilder();        
				$queryoffmodewpay->add('select','offmp')
						->add('from', '\Admin\Entity\Fmodeofpayment offmp');
				$offmodepay = $queryoffmodewpay->getQuery()->getArrayResult();
				
				///////////get offline payment mode///
			 }
			 else
			 {
				 $subuserid =0;
			 }
		///////////// Get Login user data End
		
		
          $Vid = $bsession->vid;$Trip = $bsession->Trip;$Tdate = $bsession->Tdate;$TEdate = $bsession->TEdate;$STtime = $bsession->STtime;$ETtime = $bsession->ETtime;$PCity = $bsession->PCity;$DCity = $bsession->DCity;$Cars = $bsession->Cars;$Pmode = $bsession->Pmode; $noofdays=$bsession->noofdays ;$PCityshow=$bsession->PCityshow ;$DCityshow=$bsession->DCityshow ; $copydcity =  $bsession->copydcity; $plcity = $bsession->plcity  ;
    $plfromCity  = $bsession->plfromCity;   $pltoCity =   $bsession->pltoCity;
      $plCityval=$bsession->plCityval;$plfromCityval=$bsession->plfromCityval;$pltoCityval=$bsession->pltoCityval;$Vsid =  $bsession->Vsid;$noofseats =  $bsession->noofseats;
  $pickuptext =  $bsession->pickuptext;$droptext =  $bsession->droptext;    
       
            $uid=0;
             $serviceTax = '0'; 
            // $serviceTax = '5.6'; 
               $noOfDays= $noofdays;  
            $mainarraygettripvalues=array(); 
             $scityid=   $PCity;
             $dcityid=  $DCity;
          /*    if($Trip!='0-6')
        {
              $Outstationtypeid =  $this->getapproximatedistance($scityid,$dcityid);
              if(!empty($Outstationtypeid))
              {
      $outstationid = $Outstationtypeid[0]['outstationTypeId'];
       $approx = $Outstationtypeid[0]['distance'];
              } 
        }*/
          
          
//          ///////*outsation/roundtrip start*///
           if($Trip=='1-1' && (!empty($Outstationtypeid)) )
           {  //echo "os/rt";die;
            
                $getroundtripvalues = $this->getroundtripvaluesforbooking($scityid,$dcityid, $uid,$Vid);
             //print_r($getroundtripvalues);die;
            
             for($i=0; $i<count($getroundtripvalues);$i++)
    {
         $hillcharges =  $this->gethillcharges($outstationid,$getroundtripvalues[$i]['vehicleId']);
//         ///////////get hill charges /get multipale value////
//                  
         if(!empty($hillcharges))
          {           
        $getroundtripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getroundtripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getroundtripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getroundtripvalues[$i]['hillCharge']= 0  ;
        $getroundtripvalues[$i]['stateCharge']= 0  ;
        $getroundtripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getroundtripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        
//     
//        //////////fare calculation///////
           $maxKm = 0;
          
            $minAvg = $mainarraygettripvalues[$i]['MinimumChargedDistance'] * $noOfDays;
                    if ($approx > $minAvg) {

                        $maxKm = $approx;
                    }
                    else {
                        $maxKm = $minAvg;
                    } 
           
           $basicFare = ($maxKm * $mainarraygettripvalues[$i]['perKmRate']);
             
                    $driverAllowance = $mainarraygettripvalues[$i]['driverCharges'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt'] * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge'] +  $mainarraygettripvalues[$i]['hillCharge'];
                      $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges)*$Cars  ;
                      
//                      //$apidiscount =round(($totalCharges * 5) / 100);
//                      //$totalCharges = $totalCharges-$apidiscount;
                     // $serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                    // $totalFare =  round($totalCharges + $serviceTaxApllied);
                          $totalFare =  round($totalCharges);
                                         
                       if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }                  
//              // $mainarraygettripvalues[$i]['ApiDiscount'] = $apidiscount;
//           //unset($mainarraygettripvalues[$i]['vehicleId']);     
//         
    }
                // print_r($mainarraygettripvalues);   die;         
//    
           }
//           ///////*outsation/roundtrip end*///
          
          
           
            /*outsation/oneway start*/
        
    if($Trip=='2-1' && (!empty($Outstationtypeid)))
       {
    
   ///////////get getonewaytripvalues  //////////////
       
    $getonewaytripvalues = $this->getonewayvaluesforbooking($scityid,$dcityid,$uid,$Vid);
   
     ///////////endget getonewaytripvalues  //////////////
    
     for($i=0; $i<count($getonewaytripvalues);$i++)
    {
         $hillcharges =  $this->gethillcharges($outstationid,$getonewaytripvalues[$i]['vehicleId']);
         ///////////get hill charges /get multipale value////
                  
         if(!empty($hillcharges))
          {           
        $getonewaytripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getonewaytripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getonewaytripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getonewaytripvalues[$i]['hillCharge']= 0  ;
        $getonewaytripvalues[$i]['stateCharge']= 0  ;
        $getonewaytripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getonewaytripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        //$mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
     
        //////////fare calculation///////
            $maxKm = 0;
                $minAvg =  $mainarraygettripvalues[$i]['minAvgPerDay'] *  $noOfDays;
                    if ( $approx >  $minAvg) {

                         $maxKm =  $approx;
                    }
                    else {
                         $maxKm =  $minAvg;
                    }
                    
                     $maxKm = ( $maxKm / 2);
// DO NOT DELETE IMP FOR FULL CALCULATION WITH NIGHT HALT AND CHARGES        
//var tot=((maxKm*perKmRate)+(maxKm*oneWayPerKmRate)+drivAllPerDay+nightHalt+otherCharges+hillcharges+stateCharges)*noOfCars*noOfDays;


                     $basicFare = (( $maxKm *  $mainarraygettripvalues[$i]['perKmRate']) + ( $maxKm *  $mainarraygettripvalues[$i]['oneWayPerKmRate'] ));
                     $driverAllowance = $mainarraygettripvalues[$i]['drivAllPerDay'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt']   * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge']  + $mainarraygettripvalues[$i]['hillCharge'] ;
                     $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges) * $Cars ;
                     //$apidiscount =round(($totalCharges * 5) / 100);
                     // $totalCharges = $totalCharges-$apidiscount;
                    // $serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                     $totalFare =  round($totalCharges );


//var tot = ((maxKm * perKmRate) + (maxKm * oneWayPerKmRate) + drivAllPerDay + otherCharges + hillcharges + stateCharges) * noOfCars * noOfDays;
                    if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }                  
               
          // unset($mainarraygettripvalues[$i]['vehicleId']);   
                        
         
    }
    
     // print_r($getonewaytripvalues); 
     // print_r($mainarraygettripvalues);die;
    
       }
    /*outsation/oneway end*/ 
           
        ////outsation/multicity start//
        if($Trip=='3-1')
       {
            
         //  Array ( [typeoftrip] => 3-1 [pCity] => 8 [dCity] => 25 [copydcity] => Array ( [0] => 8 [1] => 25 [2] => 8 ) [tDate] => 2015-10-26 [pickupTime] => 1:30 am [tEndDate] => 2015-10-27 [endTime] => 1:30 am [submit] => Add booking ) 
            $dcityidmainarr = array();
            ///get city id by name//////
             //$scityid = $this->getcityid($scityname);
             $scityid = $PCity;
             $dCityArr= array();
               array_push($dCityArr, $DCity);  
                    //print_r($copydcity);die;
                    
                     // echo $copydcity ; 
               if(!empty($copydcity))
               {
                   $copydcity = explode(',', $copydcity);
                  for ($i = 0; $i < sizeof($copydcity); $i++)
             {
             
                  array_push($dCityArr, $copydcity[$i]); 
                 
                    
             }
               }
 
        //print_r($dCityArr);die;
              
        // $mainarraygettripvalues=    $this->getmulticityapproximatedistance($scityid,$dCityArr,$noOfDays);
         
          $mainarraygettripvalues=    $this->getmulticityapproximatedistanceforbooking($scityid,$dCityArr,$noOfDays,$Vid);
          // print_r($mainarraygettripvalues); die;
        //  echo "<br>";echo "<br>";echo "<br>";echo "<br>";
                  if(!empty($mainarraygettripvalues))
                  {
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
              $maxKm = 0;
           
            $minAvg = $mainarraygettripvalues[$i]['MinimumChargedDistance'] * $noOfDays;
                    if ($mainarraygettripvalues[$i]['ApproxDistance'] > $minAvg) {

                        $maxKm = $mainarraygettripvalues[$i]['ApproxDistance'];
                    }
                    else {
                        $maxKm = $minAvg;
                    } 
           
           $basicFare = ($maxKm * $mainarraygettripvalues[$i]['perKmRate']);
             
                    $driverAllowance = $mainarraygettripvalues[$i]['driverCharges'] * $noOfDays;
                     $nightHaltCharges = $mainarraygettripvalues[$i]['nightHalt'] * ($noOfDays - 1);
                     $oshCharges = $mainarraygettripvalues[$i]['otherCharge'] +  $mainarraygettripvalues[$i]['hillCharge'];
                      $totalCharges = ($basicFare + $driverAllowance + $nightHaltCharges + $oshCharges)*$Cars  ;
                   // $apidiscount =round(($totalCharges * 5) / 100);
                    //  $totalCharges = $totalCharges-$apidiscount;
                      //$serviceTaxApllied = ($totalCharges * $serviceTax) / 100;
                     $totalFare =  round($totalCharges );
                                         
                       if($totalFare==0)
                       {
                          $totalFare=0;                           
                    $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }
                       else
                       {
                           $totalFare=$totalFare;                          
                          $mainarraygettripvalues[$i]['totalAmount'] = $totalFare;
                       }   
                       //unset($mainarraygettripvalues[$i]['vehicleId']);
                          //$mainarraygettripvalues[$i]['destinationCity']=$dcityname;
         }
                  }
        // print_r($mainarraygettripvalues    );die; 
        }        
        ////outsation/multicity end//     
       
        
         ////local/fulday start//
         if($Trip=='4-2')
         {
           
              
         $mainarraygettripvalues=     $this->getfulldayvaluesforbooking($scityid,$uid,$Vid);
          // print_r($mainarraygettripvalues);die;
         
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {   
            $tot = ($mainarraygettripvalues[$i]['basicFare'] )   * $noOfDays * $Cars;
                // $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                  // $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                 // unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
                  // print_r($mainarraygettripvalues);die;
        }
         
         ////local/fulday end//
        
        
         ////local/halfday start//
         if($Trip=='5-2') 
         {
             
              
         $mainarraygettripvalues=     $this->gethalfdayvaluesforbooking($scityid,$uid,$Vid);
          
         for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
            $tot = ($mainarraygettripvalues[$i]['basicFare'] )   * $noOfDays *$Cars;
                //  $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                 //  $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                 // unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
                   // print_r($mainarraygettripvalues);die;
        }
         
         ////local/halfday end//
        
        
         //transfer/airport to location start///
        if($Trip=='6-3'|| $Trip=='7-3' || $Trip=='8-3')
        {
         
          $scityid=  $plCityval  ;
          $plocid =  $plfromCityval;
          $dlocid =  $pltoCityval;
          ///get location id//
         
        
           $ctid = $scityid;          
        ////get location id//////
           
           //get distance///
           
            if($Trip=='8-3')
           {
           $distance = $this->getdistance($dlocid,$plocid,$ctid);
           }
 else {$distance = $this->getdistance($plocid,$dlocid,$ctid);}
           
           
          
               $locDistance = $distance[0]['locDistance']; 
           //get distance///
          
         //get transfer rate///
       $mainarraygettripvalues =  $this->gettransferrateforbooking($ctid,$uid,$Vid);
      // print_r($mainarraygettripvalues);  die;
       //////fare calculation start//
               for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
                //   $mainarraygettripvalues[$i]['sourceCity'] = $scityname;
                   if($locDistance <= 10)
                   {
                       $tot = ($mainarraygettripvalues[$i]['FareUpto10km'] )* $noOfDays*$Cars;
                      unset($mainarraygettripvalues[$i]['FareUpto20km']);
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                      $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto10km'];
                     unset($mainarraygettripvalues[$i]['FareUpto10km']); 
                   }
                   if($locDistance >10 && $locDistance <=20)
                   {
                       $tot = ($mainarraygettripvalues[$i]['FareUpto20km'] )   * $noOfDays*$Cars;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']); 
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto20km'];
                     unset($mainarraygettripvalues[$i]['FareUpto20km']);  
                      }
                   if($locDistance >20 && $locDistance <=40)
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto40km'] )   * $noOfDays*$Cars;
                        unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto40km'];
                     unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      }
                   if($locDistance >40 && $locDistance <=60)                       
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto60km'] )   * $noOfDays*$Cars;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto60km'];
                     unset($mainarraygettripvalues[$i]['FareUpto60km']);  
                      }
                   if($locDistance >60 && $locDistance <=80)
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareUpto80km'] )   * $noOfDays*$Cars;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareAbove100km']);
                   
                      $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareUpto80km'];
                     unset($mainarraygettripvalues[$i]['FareUpto80km']);
                   }
                   if($locDistance >80  )
                   {
                        $tot = ($mainarraygettripvalues[$i]['FareAbove100km'] )   * $noOfDays*$Cars;
                       unset($mainarraygettripvalues[$i]['FareUpto10km']);
                        unset($mainarraygettripvalues[$i]['FareUpto20km']);                       
                      unset($mainarraygettripvalues[$i]['FareUpto40km']);
                      unset($mainarraygettripvalues[$i]['FareUpto60km']);
                      unset($mainarraygettripvalues[$i]['FareUpto80km']);
                  $mainarraygettripvalues[$i]['transferBasicRate'] =  $mainarraygettripvalues[$i]['FareAbove100km'];
                     unset($mainarraygettripvalues[$i]['FareAbove100km']);  
                      }
                //    $apidiscount =round(($tot * 5) / 100);
                    //  $tot = $tot-$apidiscount;
                 //  $serviceTaxApllied =  round($tot * $serviceTax / 100);
                   $totalFare =  round($tot );
                    $mainarraygettripvalues[$i]['totalAmount']  =$totalFare   ;
     $mainarraygettripvalues[$i]['days'] = $noOfDays;
   $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
       // $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
                  //unset($mainarraygettripvalues[$i]['vehicleId']);
                  }
        //////fare calculation end///
       ////get transfer rate///
         // print_r($mainarraygettripvalues);
           //die;
        }        
//transfer/airport to location end///
           ////////////normal trip discount start////
        
        $sessiont =new Container('Triggered');
      //  echo $Trip;
        if($Trip!='0-6'){
                 $session = new Container('Discount');
                 $session->Amount = $mainarraygettripvalues[0]['totalAmount']; 
                 $session->seat=0;
                 // $sessionc=new Container('Conditions');
         $session->user=30;        
         $session->fromct=$PCity;
         $session->toct=$DCity;
         $session->bookingdate=date('Y-m-d');
         $session->startdate=$Tdate;
         $session->enddate=$TEdate;
        $session->type=$Trip;
     
  
               if((!empty($session->ServiceTax)))
 
{


            if($Trip=='1-1'){
   $Triptype='Outstation (Roundtrip)';
   $basicamount =  $mainarraygettripvalues[0]['totalAmount']; 
   $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='2-1')
 {$Triptype='Outstation (OneWay)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
  $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='3-1')
 {$Triptype='Outstation (MultiCity)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount']; 
  $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='4-2')
 {$Triptype='Local (Fullday)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='5-2')
 {$Triptype='Local (Halfday)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='6-3')
 {$Triptype='Transfer (Airport)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='7-3')
 {$Triptype='Transfer (Railway Station)';
  $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='8-3')
 {$Triptype='Transfer (Area / Hotel)';
  $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
return array('code'=>$session->code ,'trigered'=>$sessiont->triggered,'vid'=>$Vid,'trip'=>$Trip,'tdate'=>$Tdate,'tedate'=>$TEdate,'sttime'=>$STtime,'ettime'=>$ETtime,'pcity'=>$PCity,'dcity'=>$DCity,'car'=>$Cars,'pmode'=>$Pmode,'noofdays'=>$noofdays,'mainarraygettripvalues'=>$mainarraygettripvalues,'PCityshow'=>$PCityshow,'DCityshow'=>$DCityshow,'plCity'=>$plcity,'plfromCity'=>$plfromCity,'pltoCity'=>$pltoCity,
        'baseAmount'=>$session->BasicAmount,'afterdisAmt'=>  $this->basicamt,'Advance'=> $session->Advance,'tax'=> $session->ServiceTax,'Balance'=>$session->Balance,'stateCharge'=>$session->StateCharge,'Total'=>$session->TotalAmount,'triptype'=>$Triptype,'AmountPay'=>$session->Paying); 
  
}

 if($Trip=='1-1')
 {$Triptype='Outstation (Roundtrip)';
   $basicamount =  $mainarraygettripvalues[0]['totalAmount']; 
 $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='2-1')
 {$Triptype='Outstation (OneWay)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
  $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='3-1')
 {$Triptype='Outstation (MultiCity)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount']; 
  $stateCharge =  $mainarraygettripvalues[0]['stateCharge'];
 }
 if($Trip=='4-2')
 {$Triptype='Local (Fullday)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='5-2')
 {$Triptype='Local (Halfday)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='6-3')
 {$Triptype='Transfer (Airport)';
 $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='7-3')
 {$Triptype='Transfer (Railway Station)';
  $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 if($Trip=='8-3')
 {$Triptype='Transfer (Area / Hotel)';
  $basicamount =  $mainarraygettripvalues[0]['totalAmount'];
 $stateCharge=0;
 }
 
 //code to put below 1733 line
if($Trip=='0-7'){
         $dealInfo=$this->getdealDetails($dealId);
          $mainarraygettripvalues=$dealInfo;
          $PCity=$dealInfo[0]['scid'];
          $DCity=$dealInfo[0]['dstctId'];		  
		  $bsession->PCity = $PCity;
		  $bsession->DCity =  $DCity;
		  
          $PCityshow=$dealInfo[0]['SourceCity'];
          $DCityshow=$dealInfo[0]['DestinationCity'];
          $Tdate=$dealInfo[0]['tDate'];
          $triptype='';
          $mainarraygettripvalues[0]['totalAmount']=  $dealInfo[0]['dealFare'];
          $basicamount= $dealInfo[0]['dealFare'];
          $stateCharge=0;
          $plfromCity=$dealInfo[0]['srcLocName'];
          $pltoCity=$dealInfo[0]['drpLocName'];
		  
		  $em = $this->getEntityManager();
          $queryBuilder = $em->createQueryBuilder(); 
        
          $queryBuilder->add('select','ts')
                    ->add('from', '\Admin\Entity\TimeSlot ts') 
             	    ->where("ts.time  >='" . $dealInfo[0]['frmtime'] . "' AND  ts.time  <=  '".$dealInfo[0]['toTime'] . "'");
					 $tsres = $queryBuilder->getQuery()->getArrayResult();
					 
					 $bsession = new \Zend\Session\Container('Booking');
				 
}
		   
 
 $val=$mainarraygettripvalues[0]['totalAmount']*$serviceTax/100;
 $Tax=  round($val);
// echo $Tax;die;
   $total=$stateCharge+$mainarraygettripvalues[0]['totalAmount']+$Tax;
 
 $advance=0;   
 if($Pmode==2)
     {
       $advance= round($mainarraygettripvalues[0]['totalAmount']*20/100);
       $Balance =$total-$advance;    
     }
 else
  {
      $advance =$total;   
      $Balance =0;
  }
 
  
    if($advance==0)
      $amountpay=$total;
    else
        $amountpay=$advance;
    return array('code'=>'','trigered'=>$sessiont->triggered,'vid'=>$Vid,'trip'=>$Trip,'tdate'=>$Tdate,'tedate'=>$TEdate,'sttime'=>$STtime,'ettime'=>$ETtime,'pcity'=>$PCity,'dcity'=>$DCity,'car'=>$Cars,'pmode'=>$Pmode,'noofdays'=>$noofdays,'mainarraygettripvalues'=>$mainarraygettripvalues,'PCityshow'=>$PCityshow,'DCityshow'=>$DCityshow,'plCity'=>$plcity,'plfromCity'=>$plfromCity,'pltoCity'=>$pltoCity,'baseAmount'=>$basicamount,'afterdisAmt'=>  $this->basicamt,'Advance'=>$advance,'tax'=>$Tax,'Balance'=>$Balance,'stateCharge'=>$stateCharge,'Total'=>$total,'triptype'=>$Triptype,'AmountPay'=>$amountpay,'email'=>$email,'umobile'=>$umobile,'lastName'=>$lastName,'firstName'=>$firstName,'tsres'=>$tsres,'dealdrop'=>$bsession->droptext,'dealpick'=>$bsession->pickuptext,'subuserid'=>$subuserid,'offmodepay'=>$offmodepay); 
    
} 
        
        ///////////normal trip discount///end//////
         //cab shearing start///
        if($Trip=='0-6')
        {
             $session = new Container('Discount');
           $getcabsetfarevalues=array();
         
    $getcabvalues = $this->getsinglecab($Vsid);
     
    for($i=0;$i<count($getcabvalues);$i++)
         {
             $getcabsetfarevalues = $this->getseatfare($Vsid,$Tdate,$getcabvalues[$i][0]['slotId']);
             
                if(!empty($getcabsetfarevalues)) 
                {
             for($j=0;$j<count($getcabsetfarevalues);$j++)
             {
                  
             array_push($getcabvalues[$i], $getcabsetfarevalues[$j]);
             }
                }
                 
            
         }
           
          static $count  ;
            $count = count($getcabvalues);
            for($del=0;$del< $count;$del++)
            {
                 
            if(count($getcabvalues[$del])==7)
           {
                unset( $getcabvalues[$del] );
                     $getcabvalues = array_values($getcabvalues); 
                     $count = count($getcabvalues);
                     $del--;
                      //print_r($getcabvalues);die;
           }
            }
          
           
         $getcabvalues = array_values($getcabvalues); 
      
         $mainarraygettripvalues = $getcabvalues;
    
          $bsession = new \Zend\Session\Container('Booking');
         $bsession->pickuptme = date('h:i a ', strtotime($mainarraygettripvalues[0]['time']));
		 
		 
		 
  ///////discount for share cab///////
         
          if((!empty($session->ServiceTax)))
 {
//  echo "here";die; 
$basefare=$session->TotalAmount-$session->ServiceTax;
    //echo "ithe".$session->Amount;die;
     //echo $basefare;die;
      return array('code'=>$session->code,'trigered'=>$sessiont->triggered,'vid'=>$Vid,'trip'=>$Trip,'tdate'=>$Tdate,'tedate'=>$TEdate,'sttime'=>$STtime,'ettime'=>$ETtime,'pcity'=>$PCity,'dcity'=>$DCity,'car'=>$Cars,'pmode'=>$Pmode,'noofdays'=>$noofdays,'mainarraygettripvalues'=>$mainarraygettripvalues,'PCityshow'=>$PCityshow,'DCityshow'=>$DCityshow,'plCity'=>$plcity,'plfromCity'=>$plfromCity,'pltoCity'=>$pltoCity, 'shareTrip'=>$Trip,'noofseats'=>$noofseats,'droptext'=>$droptext,'pickuptext'=>$pickuptext,'basefare'=>$basefare,'st'=>$session->ServiceTax,'total'=>$session->TotalAmount,'email'=>$email,'umobile'=>$umobile,'lastName'=>$lastName,'firstName'=>$firstName,'tsres'=>$tsres,'dealdrop'=>$bsession->droptext,'dealpick'=>$bsession->pickuptext,'subuserid'=>$subuserid,'offmodepay'=>$offmodepay);  

      
     
 }
       else
 {
         //echo "here";die;
         $seatarr=explode(",",$noofseats);
 //echo count($seatarr);die;
 $basefare=0;
 for($k=0;$k<Count($seatarr);$k++)
{
   $basefare = $basefare + $mainarraygettripvalues[0][$seatarr[$k]]['fare'];
}
//$st=round($basefare*5.6/100);
$st = '0'; 
//die;
$total=$st+$basefare;
    
     $session->seat=count($seatarr);
     $session->Amount = $basefare;
     $session->type=$Trip;
 return array('code'=>'','trigered'=>$sessiont->triggered,'vid'=>$Vid,'trip'=>$Trip,'tdate'=>$Tdate,'tedate'=>$TEdate,'sttime'=>$STtime,'ettime'=>$ETtime,'pcity'=>$PCity,'dcity'=>$DCity,'car'=>$Cars,'pmode'=>$Pmode,'noofdays'=>$noofdays,'mainarraygettripvalues'=>$mainarraygettripvalues,'PCityshow'=>$PCityshow,'DCityshow'=>$DCityshow,'plCity'=>$plcity,'plfromCity'=>$plfromCity,'pltoCity'=>$pltoCity, 'shareTrip'=>$Trip,'noofseats'=>$noofseats,'droptext'=>$droptext,'pickuptext'=>$pickuptext,'basefare'=>$basefare,'st'=>$st,'total'=>$total,'email'=>$email,'umobile'=>$umobile,'lastName'=>$lastName,'firstName'=>$firstName,'tsres'=>$tsres,'dealdrop'=>$bsession->droptext,'dealpick'=>$bsession->pickuptext,'subuserid'=>$subuserid,'offmodepay'=>$offmodepay);  
      
        }      
         
         ///discount for share cab///end///////////
        }
		
		
    }
    
    
    public function confirmbookingAction()
    { 
        $loginenv=$this->getenv();
		//print_r($_POST);die;
         $hosturlCurrnet=$_SERVER['HTTP_HOST'];
        $finalurl=$this->getRealUrl();
         $aws = $this->getServiceLocator()->get('aws');
        $client = $aws->get('ses');
         $em2 = $this->getEntityManager2();
        // $serviceTax='5.6';
         $serviceTax='0';
		 
		 if($_POST['fullname']=='')
		 {
		    $_POST['fullname']=$_POST['Passengername'][0];
		    $_POST['lname']=$_POST['Passengername1'][0];
		 }
		  $bsession = new \Zend\Session\Container('Booking' );
		    		 $bsession->fulname=$_POST['fullname']." ".$_POST['lname'];
                     $bsession->email=$_POST['email'];
					 $bsession->mobile=$_POST['mobile'];
 		 
        		$em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'sup')
                        ->add('from', '\User\Entity\Signup sup')
                        ->where("(sup.email)= '" .  $_POST['email'] . "'  ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
                if (count($result) == 0) {
                    ///for new user//
                
                     $arrData['security']='123';
                     $arrData['cid']='123';
                     $arrData['subuserid']='4';
                     $arrData['firstName']=$_POST['fullname'];
                     $arrData['lastName']=$_POST['lname'];
                     $arrData['email']=$_POST['email'];
                      $arrData['mobile']=$_POST['mobile'];
                      $arrData['ageGroup']=0;
                     
                      
//                       $arrData['sNuId']=1;
//                        $arrData['parentId']=1;
                     $signup = new Signup();   
                    // print_r($arrData);
                     $signup->setsNuId(1);
					$signup->setparentId(1);
        $signup->exchangeArray($arrData);
      $this->getEntityManager()->persist($signup);
      $this->getEntityManager()->flush();
      
      
      //Start Permission add
                    
                  $recentid = $signup->uId;
                    $recentsubuserid = $signup->subuserid;
                    
                    $userroles = new UserRoles();
                    $userroles->user_id =$recentid;
                    $userroles->role_id =$recentsubuserid;
                    $this->getEntityManager()->persist($userroles);
                    $this->getEntityManager()->flush();
                    //End Permission add
      
          $admindata = $em->getRepository('User\Entity\Signup')->find(1);
                    // print_r($admindata);
//                    $adminid = $admindata->sNuId;
//                     $recentid = $signup->uId;
//                    $passwordval = $signup->passwStr;
//                    $arrData['uId'] = $recentid;
//                    $cid = sprintf("%04d", $recentid);
//                    $cidCode = "CID" . $adminid . $cid;
//                    $signup->setCidCode($cidCode);
                    //$signup->setsNuId($useridval);
                   // $signup->setparentId($parentIdval);
          
                       $adminid = $admindata->sNuId;
                    $recentid = $signup->uId;
                    $passwordval = $signup->passwStr;
                    $arrData['uId'] = $recentid;
                    $cid = sprintf("%04d", $recentid);
                    $cidCode = "CID" . $adminid . $cid;
                    $signup->setCidCode($cidCode);
                    //$signup->setsNuId($useridval);
                   // $signup->setparentId($parentIdval);
                    $this->getEntityManager()->persist($signup);
                    $this->getEntityManager()->flush();
//                    $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
//                    $userId = $authService->getIdentity()->email;
//                    $success = $em2->getRepository('Registration\Entity\Registration')->findOneByemailId($userId);
//                    $loginurl1 = $success->loginUrl;
//                    $loginurl = "https://" . $loginurl1 . "/signin";
//                     $loginurllogo = "https://" . $loginurl1.$logopath;
                    
                    
                    $hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        if($companyentity->logo!="")
        {
            $userCompanyLogo=$companyentity->logo;
        }
        else
        {
            $userCompanyLogo="logo.jpg";
        }
        $logopath='/images/company/clogo/'.$userCompanyLogo;
         if($loginenv=="cabsaas")
                        {
         $loginurl = "https://" . $hosturlCurrnet . "/signin";
        $loginurllogo = "https://" . $loginurl1.$logopath;
                        }
                        else
                        {
                            $loginurl = "http://" . $hosturlCurrnet . "/signin";
        $loginurllogo = "http://" . $loginurl1.$logopath;
                        }
       // $companynameVal=$userCompanyName;
                    
                    
                    $emailidVal = $arrData['email'];
          
                    $this->getEntityManager()->persist($signup);
                    $this->getEntityManager()->flush();
                    
                     $resultMail = $client->sendEmail(array(
                        // Source is required
                        'Source' => '"'.$userCompanyName.'" <noreply@clearcarrental.com>',
                        // Destination is required
                        'Destination' => array(
                            'ToAddresses' => array($emailidVal),
							'BccAddresses' => array('shriram.chaudhari@infogird.com','jaidevcoolcab@gmail.com'),
                        ),
                        // Message is required
                        'Message' => array(
                            // Subject is required
                            'Subject' => array(
                                // Data is required
                                'Data' => "$userCompanyName Login Url",
                                'Charset' => 'UTF-8',
                            ),
                            // Body is required
                            'Body' => array(
                                'Text' => array(
                                    // Data is required
                                    'Data' => 'Hello Guys how all you feel when using ZF2',
                                    'Charset' => 'UTF-8',
                                ),
                                'Html' => array(
                                    // Data is required
                                    'Data' => '<title>Registration</title>

</head>

<body>
<div style="width:600px; height:auto; float:left; font-family:Calibri, Arial; background:#eaeaea; padding:10px;">

<div style="width:598px; height:auto; float:left; background:#fff; border:1px solid #cccccc;">
 
<div style="width:100%; height:auto; float:left; background:#41b1b0;  padding:15px 0px 15px 0px; margin-bottom:10px;">

<div style="width:200px; height:100%; float:left; display:inline-block; border-radius:40px; background:#fff;  margin-left:20px; clip-path: circle(60px at center); vertical-align:middle; text-align:center; padding:10px; "> <img height="47" width="175" src="'.$loginurllogo.'"  /> </div>

<div style="width:auto; height:50px; line-height:50px; float:left; margin-left:20px; padding:10px; font-weight:bold; font-size:30px; color:#fff;">Welcome to '.$userCompanyName.' !</div>

</div>
 
<div style="width:570px; height:auto; float:left; font-size:18px; color:#666666; text-align:left; margin-left:15px; ">
Dear User,<br /><br />
Welcome to '.$userCompanyName.' !
<br /><br />

 
 
<b>Your website details are as follows:</b><br /><br />

<div style="width:90%; height:auto;  float:left; padding:0px 5% 0px 5%;">Site URL: <a href="' . $loginurl . '">' . $loginurl . '</a> <br /><br />

User name: ' . $emailidVal . '<br /><br />

Password: ' . $passwordval . '<br /><br />


</div>
<div style="width:100%; height:auto;  float:left; margin:2% 0% 2% 0%;"> 
Once again, thanks for registering with us. We look forward to a fruitful relation with your organization. We will be always available to help you.
</div>

<b>- Team '.$userCompanyName.'</b>

</div>
</body>
</html>',
                                    // 'Data' => 'Your Login Url is "'.$loginurl.'"',
                                    'Charset' => 'UTF-8',
                                ),
                            ),
                        ),
                            //    'ReplyToAddresses' => array('noreply@clearcarrental.com'),
                            //    'ReturnPath' => 'suppport@clearcarrental.com',
                    )); 
                    
                    
                }
                
                    if(empty($result))
                    {
                     $recentid=$recentid;
                    }
                    else
                    {
                      $recentid=  $result[0]['uId'];
                    }
                    
                    
                    
                    
                    
                    /////////check alredy booked/payment done//////
					 $bsession = new \Zend\Session\Container('Booking' );
					 //for cab share///
                    if($bsession->Trip=='0-6')
                   {
       $seat =  $bsession->noofseats;
        $seat= ltrim ($seat, ',');
         $seats =   explode(',', $seat);
         $seatstatus='';
         $bsession = new \Zend\Session\Container('Booking');
                      $Vsid = $bsession->Vsid;
                      $flag=0;
        for($i=0;$i<count($seats);$i++)
        {
            ///////////chk other user book same seat or not////////
            $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'sha')
                        ->add('from', '\Tariff\Entity\Shared sha')
                        ->where(" sha.vShId='" . $Vsid. "'  AND   sha.seatNo=  '" . $seats[$i] . "'  AND   sha.status=  1  AND sha.tdate='" .$bsession->Tdate . "'  ")
                        ->getQuery();
                $resultalredybook = $queryBuilder->getQuery()->getArrayResult();
                if(!empty($resultalredybook))
                {$flag=10;}
            ///chk other user book same seat or not///
            
            $seatstatus = $seatstatus.','.$seats[$i].'-'.'1';
        }
         $seatstatuss= ltrim ($seatstatus, ',');
         
         if($flag==10)
         {
             echo "<script>alert('alredy booked by other user!'); </script>";  
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'index'
                ));
          //////redirect code here///
         }
				   }
				   /////for cab share///
         
//        $em = $this->getEntityManager();
//                $queryBuilder = $em->createQueryBuilder();
//                $queryBuilder->add('select', 'bshare')
//                        ->add('from', '\Booking\Entity\Bookingshare bshare')
//                        ->where(" bshare.uId='" . $bsession->uid . "'  AND   bshare.status=  '" . $seatstatuss . "'  AND bshare.seatNo ='" . $seat . "' ")
//                        ->getQuery();
//                $result = $queryBuilder->getQuery()->getArrayResult();
        if(1)
        {
                          
                    
                    //////////cab shere/start//
					//for cab share/
					 $bsession->uid = $recentid;
					if($bsession->Trip=='0-6')
					{
                    $bsession = new \Zend\Session\Container('Booking');
                      $Vsid = $bsession->Vsid;  
                    $Tdate =  $bsession->Tdate;
         $bsession->uid = $recentid;
                   
                    
                    $getcabsetfarevalues=array();         
    $getcabvalues = $this->getsinglecab($Vsid);     
    for($i=0;$i<count($getcabvalues);$i++)         {
             $getcabsetfarevalues = $this->getseatfare($Vsid,$Tdate,$getcabvalues[$i][0]['slotId']);             
                if(!empty($getcabsetfarevalues)) 
                {
             for($j=0;$j<count($getcabsetfarevalues);$j++)
             {                  
             array_push($getcabvalues[$i], $getcabsetfarevalues[$j]);
             }
                }          
         }           
          static $count  ;
            $count = count($getcabvalues);
            for($del=0;$del< $count;$del++)
            {                 
            if(count($getcabvalues[$del])==7)
           {                unset( $getcabvalues[$del] );
                     $getcabvalues = array_values($getcabvalues); 
                     $count = count($getcabvalues);
                     $del--;
                      //print_r($getcabvalues);die;
           }
            }          
         $getcabvalues = array_values($getcabvalues);       
         $mainarraygettripvalues = $getcabvalues;
         
         $noofseats = $bsession->noofseats ;
         $seatarr = explode(',', $noofseats);
          $basefare=0;
          $perseatfare='';
          $status='';
          $pasengername='';
          $gender='';
for($k=0;$k<count($seatarr);$k++) 
{
     $basefare = $basefare + $mainarraygettripvalues[0][$seatarr[$k]]['fare'];
      $perseatfare=$perseatfare.','.$mainarraygettripvalues[0][$seatarr[$k]]['fare'];
      $status = $status.','.$seatarr[$k].'-'.'0';
}
          $perseatfares= ltrim ($perseatfare, ',');  
       $statuss= ltrim ($status, ','); 
					}
					//for cab share/////////
					
					if($bsession->Trip=='0-6')
					{
       ////////check for alredy exist booking///
        $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bookadd')
                        ->add('from', '\Booking\Entity\Bookingadd bookadd')
                        ->where("(bookadd.vehicleId)= '" .  $bsession->vid . "'   AND bookadd.pCity=  '" . $bsession->PCity . "' AND bookadd.dCity=  '" . $bsession->DCity . "' AND bookadd.tDate=  '" . $bsession->Tdate . "' AND bookadd.pickupTime=  '" . $bsession->pickuptme . "'   ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
					}
					else{ $result=''; }
                //for cab share/////////
       ////////check for alredy esist booking/end ///
       if(empty($result)){ 
                     $bookData['subuserType']='4';
                     $bookData['bookedByUid']= $recentid;
                      $bsession = new \Zend\Session\Container('Booking');
                      
                      $bookData['bookingDateTime'] =    date('Y-m-d h:i:s');
                      $bookData['pCity'] = $bsession->PCity;
                      $bookData['dCity'] = $bsession->DCity;
                         $bookData['vehicle'] = $bsession->vid;
						
						if($bsession->Trip=='0-6')
						{
						   $bookData['travelRoot'] = 11;
						   $bookData['travelType'] = 6;
						   $bookData['approxDistance'] = $mainarraygettripvalues[0][0]['distance'];
						   $bookData['minDistancePerDay'] = $mainarraygettripvalues[0][0]['distance'];
						   $bookData['basicFare'] =       $mainarraygettripvalues[0][0]['basicFare'];
						   $bookData['totalBasicFare']=  $mainarraygettripvalues[0][0]['basicFare'];
							$bookData['tDate'] = $bsession->Tdate;
						    $bookData['tEndDate'] = $bsession->Tdate;
							$bookData['uId']= 1; 
							$bookData['pickupTime'] = $bsession->pickuptme;
                                                        
                                                        
                                                        
                                                        
                                                        $bookData['kmRate']=  $mainarraygettripvalues[0][0]['extKm'];


                                                        
							
						}
                       if($bsession->Trip=='0-7')
						{
							   $bookData['travelRoot'] = 12;
							   $bookData['travelType'] = 7;
							   
							   //$bsession = new \Zend\Session\Container('Booking');
							   $getdealDetails = $this->getdealDetails($bsession->dealId) ;
							   
							   $bookData['approxDistance'] = $getdealDetails[0]['distance'];
							   $bookData['minDistancePerDay'] = $getdealDetails[0]['distance'];
							   $bookData['basicFare'] =       $getdealDetails[0]['basicFare'];
							   $bookData['totalBasicFare']=  $getdealDetails[0]['dealFare'];
							   $bookData['uId']= $recentid; 
							   $bookData['pickupTime'] = $_POST['ptime'];					   
								 $bookData['tDate'] = $getdealDetails[0]['tDate'];
								 $bookData['tEndDate'] =$getdealDetails[0]['tDate'];
                                                                  $bookData['kmRate']=  $getdealDetails[0]['extKm'];

 					 	 
						}
					   $bookData['status'] =0;
					   $bookData['noOfDays'] = 1;
                       $bookData['noOfCars'] = 1;
                       $bookData['ipId'] = $_SERVER['REMOTE_ADDR'];
                       //    $bookData['fullDay'] =$basefare; 
                      $bookData['serviceTax']= $serviceTax;
                       //  $bookData['totalBasicFare']= round($basefare+(    $basefare*$serviceTax)/100);
                     
                      //  print_r($bookData);
                      
				 

                        $Bookingadd = new  Bookingadd(); 
                        $Bookingadd->exchangeArray($bookData);
                    $this->getEntityManager()->persist($Bookingadd);
                    $this->getEntityManager()->flush();
                    $recentbookid =  $Bookingadd->bookingId;
                    
                    /////////add ref no//////////
                    
                     $today = date("Y-m-d");
                $queryBuilder->add('select', 'count(b) as countID')
                        ->add('from', '\Booking\Entity\Bookingadd b')
                        ->where("b.bookingDateTime Like '%" . $today . "%'");

                $result = $queryBuilder->getQuery()->getArrayResult();
                 $Bookings = new  Bookingadd(); 
                $bookings = $this->getEntityManager()->find('Booking\Entity\Bookingadd', $recentbookid);
                
				           if($bsession->Trip=='0-6')
						   {
                          $genrefno =   $this->crngen($result[0]['countID'], 11);
						   }
						    if($bsession->Trip=='0-7')
						   {
						   $genrefno =   $this->crngen($result[0]['countID'], 12);
						   }
                              $bookings->refNo = $genrefno; 
                              $this->getEntityManager()->persist($bookings);
                $this->getEntityManager()->flush();
				$bsession->genrefno = $genrefno;
                    ///// //add ref no/end///////
                    
                } 
                else
                {
                    $recentbookid =  $result[0]['bookingId'];
                    $genrefno =  $result[0]['refNo'];
                }
                    
                  $genrefno = $genrefno.date("h:i");
                   
                   $bsession = new \Zend\Session\Container('Booking' );
         $bsession->recentbookid = $recentbookid;
                   
                   
                   
                ///////add travellerdetails////
				////////////////find already exist user and bid /////////
               
			    $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->add('select', 'bdtrvler')
                        ->add('from', '\Booking\Entity\Bookingtraveller bdtrvler')
                        ->where("(bdtrvler.bookingId)= '" .$recentbookid. "'   AND bdtrvler.uId=  '" .$recentid. "'")
                        ->getQuery();
                $result1 = $queryBuilder1->getQuery()->getArrayResult();
				 
                if(empty($result1))
				  {
					$travellerData['bookingId']=$recentbookid;
					$travellerData['travellerName']=$_POST['fullname'].' '.$_POST['lname'];         
					$travellerData['travellerContactNo']=$_POST['mobile'];
					$travellerData['travellerEmailId']=$_POST['email'];                 
					$travellerData['addressLine1']=$_POST['addressone'];
					$travellerData['addressLine2']=$_POST['addresssecond'];
					$travellerData['landMark']=$_POST['landmark'];
					$travellerData['location']=$_POST['location'];
					$travellerData['pinCode']=$_POST['pincode'];
					$travellerData['uId']= $recentid;
					$Bookingtraveller = new  Bookingtraveller(); 
					$Bookingtraveller->exchangeArray($travellerData);
					$this->getEntityManager()->persist($Bookingtraveller);
					$this->getEntityManager()->flush();                
				}
				
                //////////travellerdetails end///////////
                 
                    //////////user contact details start///////////
                    $UserAddress = new  UserAddress(); 
                     $ucontactData['addressType']= '5';
                     $ucontactData['city']= $bsession->PCity;
                     $ucontactData['address1']=$_POST['addressone'];
                 $ucontactData['address2']=$_POST['addresssecond'];
                 $ucontactData['landmark']=$_POST['landmark'];
                 $ucontactData['location']=$_POST['location'];;
                 $ucontactData['pincode']=$_POST['pincode'];
                 $UserAddress->setuser($recentid); 
                  //$ucontactData['user']=0;
                 // $UserAddress = new  UserAddress(); 
                        $UserAddress->exchangeArray($ucontactData);
                    $this->getEntityManager()->persist($UserAddress);
                    $this->getEntityManager()->flush();   
//                       
                    ///user contact details end////
                    
                ////Add booking share table /////////
                    
					
					///////////Check already exist ////////////////
				
				
				if($bsession->Trip=='0-6')
				{
					      for($i=0;$i<count($_POST['Passengername']);$i++) 
							{                
							$pasengername=$pasengername.','.$_POST['Passengername'][$i].' '.$_POST['Passengername1'][$i];
							
							$gender=$gender.','.$_POST[$i.'gender'] ;
							}
							$pasengernames= ltrim ($pasengername, ',');           
							$genders= ltrim ($gender, ',');
								    
							$bsession = new \Zend\Session\Container('Booking' );
							$bsession->genders = $genders;
					
							$em = $this->getEntityManager();
							$queryBuilderbs = $em->createQueryBuilder();
							$queryBuilderbs->add('select', 'bshare')
									->add('from', '\Booking\Entity\Bookingshare bshare')
									->where("(bshare.bookingId)= '" .$recentbookid. "'   AND bshare.seatNo=  '" .$noofseats. "'   AND bshare.uId=  '" .$recentid. "'")
									->getQuery();
							$resultbs = $queryBuilderbs->getQuery()->getArrayResult();
				}
				else
				{
					$resultbs='';
				}
										///////////Check already exist ////////////////
					if(empty($resultbs))
					{  							
							$bookshareData['bookingId']= $recentbookid;
							
							if($bsession->Trip=='0-7')
				             {								 
							    $getdealDetails = $this->getdealDetails($bsession->dealId);					   
					          // 'dealdrop'=>$bsession->droptext,'dealpick'=>$bsession->pickuptext
							    $bookshareData['seatNo']= $bsession->dealId;
 								$bookshareData['dropLocId']= $bsession->drop;
								$bookshareData['pickLocId']= $bsession->pickup;
								$bookshareData['distance']= $getdealDetails[0]['distance'];
								$bookshareData['totalFare']= $getdealDetails[0]['dealFare'];
								$basefare= $getdealDetails[0]['dealFare'];
								 
						     }
							 else if($bsession->Trip=='0-6')
							 {
 								$bookshareData['dropLocId']= $bsession->drop;
								$bookshareData['pickLocId']= $bsession->pickup;
								$bookshareData['seatNo']= $noofseats;
								$bookshareData['gender']= $genders;
								$bookshareData['travelerName']= $pasengernames;
								$bookshareData['status']= $statuss;
								$bookshareData['distance']= $mainarraygettripvalues[0][0]['distance'];
								$bookshareData['totalFare']= $mainarraygettripvalues[0][0]['basicFare'];
								$bookshareData['farebysheet']= $perseatfares;   
							 }
							 
 							$bookshareData['uId']= $recentid;                  
							$Bookingshare = new  Bookingshare(); 
							$Bookingshare->exchangeArray($bookshareData);
							
							$this->getEntityManager()->persist($Bookingshare);
							$this->getEntityManager()->flush();    
					}
					else
					{
							$updatebs = $this->getEntityManager()->getRepository('\Booking\Entity\Bookingshare')->findOneBy(array('bookingId'=>$recentbookid,'seatNo'=>$noofseats,'uId'=>$recentid) );  
 							$updatebs->dropLocId=$bsession->drop;
							$updatebs->pickLocId= $bsession->pickup;
							$updatebs->gender=$genders; 							
							$updatebs->travelerName=$pasengernames; 
							$this->getEntityManager()->flush();  
					}
					
                    ///////booking share table end////
					
					
					//////make status 2 for procrssing deal start //////
					if($bsession->Trip=='0-7')
				             {	
					$updatedealstatus = $this->getEntityManager()->getRepository('\Tariff\Entity\Deals')->findOneBy(array('dId'=>$bsession->dealId));  
 							$updatedealstatus->status=2;						 
                            $updatedealstatus->processtime= date('Y-m-d h:i:s');
                            $updatedealstatus->currentsession= $bsession->getManager()->getId();
							$this->getEntityManager()->flush();
							 }
					////////make status 2 for procrssing deal end ///
					
					
                    
					 if($bsession->Trip=='0-6')
					 {
							////////////Discount add start //////////
							  $DisSession = new Container('Discount'); 
							  $sessiontd =new Container('Triggered'); 
							  if($sessiontd->triggered==1)
					  			{
						  $DisAmountpay = $DisSession->Amountpay;   // Final paid amount after discount apply + stax
						  $DisCouponId = $DisSession->DisCouponId; // Discount Id
						  $DisValue = $DisSession->DisValue; // Discount Value
						  
						  $basefare=$DisSession->DisBasicAmt; 
						   $em = $this->getEntityManager(); 
						   $data = array(
										"uid" =>$recentid,
										"bid" => $recentbookid,
										"couponId" => $DisCouponId,
										 "Dval"=>$DisValue,
									);
						    
							$dtype = new UserCouponLog();
							$dtype->exchangeArray($data);
						 //  print_r($dtype->exchangeArray); die;
							$this->getEntityManager()->persist($dtype);
							$this->getEntityManager()->flush(); 
					  } 
					 }
					  
					 
				   ////////////Discount add end //////////
					
                    ///////////payment process start////
                    // Merchant key here as provided by Payu
                   // Value of a sample key - gtKFFx
//Value of a sample salt - eCwWELxi
	  
						if($bsession->Trip=='0-7')
				             {	
							 	 if($_POST['paidMode']==0)
								   {
									   $basefare=$basefare*0.25;
								   } 
							 }
							 
		  $bookshareData['totalFare'] = round($basefare+($basefare*$serviceTax)/100);
		  
		  	//////offline payment /////
		   $subuserid= $bsession->subuserid;
		  $bsession->offlinepayamt =  $bookshareData['totalFare']; 
                     $bsession->paymodeoption = $_POST['agree'][0];
                
                  if($subuserid==1 || $subuserid==6 )
  					 {
                        $bsession->offlinepmode= $_POST['offlinepmode'];
                         $bsession->noteformode=$_POST['noteformode'];
			 
			 
                      
         return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocessdone'
                ));             
                      
   }
   
  
   ///////////////for customer wallet option///////
                else if($subuserid==4 && $bsession->paymodeoption==2 )
  					 {          
         return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocessdone'
                ));             
                      
   }
   
   		
   				//////offline payment end  /////
                  else
					 { 
		
					$MERCHANT_KEY = $this->PG_MERCHANT_KEY; 						
					$SALT = $this->PG_SALT; 
		
		$hosturl=$_SERVER['HTTP_HOST'];
		$successUrl="http://".$hosturl."/website/index/paymentprocessdone";
		$failUrl="http://".$hosturl."/website/index/paymentprocessfail";
			
		$_POST['key'] = $MERCHANT_KEY;
		$_POST['txnid'] = $genrefno;
		$_POST['amount'] =  $bookshareData['totalFare'];
		$_POST['firstname'] =$_POST['fullname'].' '.$_POST['lname'];
		$_POST['email'] =  $_POST['email'];
		$_POST['phone'] =  $_POST['mobile'];
		$_POST['productinfo']='Car Rental Service';
		$_POST['surl']=$successUrl;
		$_POST['furl']=$failUrl; 
		// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://secure.payu.in";

$action = '';
//if(!empty($_POST)) {
$posted = array();
if(!empty($_POST)) {
//print_r($_POST);
foreach($_POST as $key => $value) {

$posted[$key] = htmlentities($value, ENT_QUOTES);
}
}

$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
if(
empty($posted['key'])
|| empty($posted['txnid'])
|| empty($posted['amount'])
|| empty($posted['firstname'])
|| empty($posted['email'])
|| empty($posted['phone'])
|| empty($posted['productinfo'])
|| empty($posted['surl'])
|| empty($posted['furl'])
) {
$formError = 1;
} else {
$hashVarsSeq = explode('|', $hashSequence);
$hash_string = '';
foreach($hashVarsSeq as $hash_var) {
$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
$hash_string .= '|';
}
$hash_string .= $SALT;
$hash = strtolower(hash('sha512', $hash_string));
$action = $PAYU_BASE_URL . '/_payment';
}
} elseif(!empty($posted['hash'])) {
$hash = $posted['hash'];
$action = $PAYU_BASE_URL . '/_payment';
}
 
//die;
$psession = new \Zend\Session\Container('Payment' );
$psession->hash=$hash;
$psession->genrefno=$genrefno;
$psession->amount=$bookshareData['totalFare'];
$psession->fname=$_POST['fullname'].' '.$_POST['lname'];
$psession->email=$_POST['email'];
$psession->contactno=$_POST['mobile'];
 return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocess'
                ));
////////payment process end/////////
					}
                    ////cab shere///end//
        }
        else
        {
			echo "<script>alert('alredy booked by other user !'); </script>";   
		  
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'index'
                ));
          //////redirect code here///
          }
        
    }
    
     public function paymentprocessAction()
    {
		 $this->getRealUrl();
         $psession = new \Zend\Session\Container('Payment' );
         return array('hash'=>$psession->hash,'genrefno'=>$psession->genrefno,'amount'=>$psession->amount,'fname'=>$psession->fname,'email'=>$psession->email,'contactno'=>$psession->contactno);
     
  }
   
     public function paymentprocessdoneAction()
     {  
        $loginenv=$this->getenv();
 
         //////////check alredy payment/boking done /////
             $this->getRealUrl();
        ////////////////
        
             $bsession = new \Zend\Session\Container('Booking' );
              $subuserid= $bsession->subuserid;
             
         $txnRs = array();
	if(!empty($_POST) || ( $subuserid==1 || $subuserid==6 || $subuserid==4 ) ) 
	  {  
				foreach($_POST as $key => $value)
				 { 
					$txnRs[$key] = htmlentities($value, ENT_QUOTES);
				 }
				 
				if($subuserid==1 || $subuserid==6 || $subuserid==4)
				{
				  $txnRs['status']='success';
				  }
                                 
				if($txnRs['status']=='success' )
				   {
                                    
 	  $SALT = $this->PG_SALT; 
      $merc_hash_vars_seq = explode('|', "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10");
      //generation of hash after transaction is = salt + status + reverse order of variables
       $merc_hash_vars_seq = array_reverse($merc_hash_vars_seq);
       $merc_hash_string = $SALT . '|' . $txnRs['status'];
       foreach ($merc_hash_vars_seq as $merc_hash_var) {
       $merc_hash_string .= '|';
        $merc_hash_string .= isset($txnRs[$merc_hash_var]) ? $txnRs[$merc_hash_var] : '';
       }
      
      $merc_hash =strtolower(hash('sha512', $merc_hash_string));
      
       if($subuserid==1 || $subuserid==6 || $subuserid==4)
       {
           $merc_hash=1;
           $txnRs['hash']=1;
       }
      
      if($merc_hash!=$txnRs['hash'])
	  {
             echo "<script>alert('Error occured please contact support team 1!'); </script>";
          
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocessfail'
                ));
      } 
 	else
	  {
            $bsession = new \Zend\Session\Container('Booking' );
       $seat =  $bsession->noofseats;
        $seat= ltrim ($seat, ',');
         $seats =   explode(',', $seat);
         $seatstatus='';
        for($i=0;$i<count($seats);$i++)
        { 
            
            $seatstatus = $seatstatus.','.$seats[$i].'-'.'1';
        }
         $seatstatuss= ltrim ($seatstatus, ',');
         
         
        $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bshare')
                        ->add('from', '\Booking\Entity\Bookingshare bshare')
                       ->where(" bshare.uId='" . $bsession->uid . "'  AND   bshare.status=  '" . $seatstatuss . "' AND 'bookingId'= '" . $bsession->recentbookid . "' ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
        if(empty($result))
        {
         
                   $bsession = new \Zend\Session\Container('Booking' );
       $Vsid= $bsession->Vsid; $noofseats= $bsession->noofseats ;  $genders= $bsession->genders;  
        $noofseats= ltrim ($noofseats, ',');
         $noofseatsarr =   explode(',', $noofseats);
            $gender =   explode(',', $genders);
          $status='';
        for($i=0;$i<count($noofseatsarr);$i++)
        {            
            $status = $status.','.$noofseatsarr[$i].'-'.'1'; 
            //$statusChk =    new \Tariff\Entity\Shared();
            $statusChk = $this->getEntityManager()->getRepository('\Tariff\Entity\Shared')->findOneBy(array('vShId'=>$Vsid,'seatNo'=>$noofseatsarr[$i],'tdate'=>$bsession->Tdate) ); 
            $statusChk->status=1;
             $statusChk->bookedby=$gender[$i];              
            $statusChk->processtime= date('Y-m-d h:i:s');
            $statusChk->currentsession= $bsession->getManager()->getId();
                    $this->getEntityManager()->flush();     
       }
        $statuss= ltrim ($status, ',');
         $recentbookid =  $bsession->recentbookid;
        $statusconfirm= $this->getEntityManager()->getRepository('Booking\Entity\Bookingshare')->findOneBy(array('uId'=>$bsession->uid,'seatNo'=>$noofseats,'bookingId'=>$recentbookid) ); 
        $statusconfirm->status=$statuss;
         $this->getEntityManager()->flush(); 
       
	   
	   /////////////// Update booking status start ///////////////
	    $bookstatuschange= $this->getEntityManager()->getRepository('Booking\Entity\Bookingadd')->findOneBy(array('bookingId'=>$bsession->recentbookid) ); 
        $bookstatuschange->status=1;
         $travelRootvalue=$bookstatuschange->travelRoot;
        $this->getEntityManager()->flush();
         $bookstatuschange->travelRoot;
	   /////////////// Update booking status end ///////////
	   
	    /////////////// Update bookingshare status for deal start ///////////////
	   if($bsession->Trip == '0-7')
	   {	  
	    //////make status 1 for confirm deal start //////
					 
					$updatedealstatus = $this->getEntityManager()->getRepository('\Tariff\Entity\Deals')->findOneBy(array('dId'=>$bsession->dealId));  
 							$updatedealstatus->status=1;
							$this->getEntityManager()->flush();
							 
					////////make status1 for confirmdeal end ///
	   }
	   /////////////// Update bookingshare status for deal end ///////////
	   
	   
         ////entry on booking payment///
         $bookpaymentData['refNo']= $bsession->recentbookid;
          $bookpaymentData['paymentDateTime']=  date('Y-m-d h:i:s');            
		  if($subuserid==1 || $subuserid==6 || $subuserid==4)
   {
		  $bookpaymentData['paymentTypeId']= 7;
                  $bookpaymentData['paymentComment']='Offline Payment';
   }
   
   else
   {
        $bookpaymentData['paymentTypeId']=  6;
        $bookpaymentData['paymentComment']='Online Payment';
   }
         $bookpaymentData['paymentRefId']= 'dummyid';//$txnRs['txnid']
        $bookpaymentData['uId']= $bsession->uid; 
        
        $psession = new \Zend\Session\Container('Payment' );
        
		 if($subuserid==1 || $subuserid==6)
       {
          $bookpaymentData['amount']=$bsession->offlinepayamt;
		  $tempCod=$bsession->offlinepmode;
	   }
	   else if ($subuserid==4)
	   { 		   
          $bookpaymentData['amount']=$bsession->offlinepayamt;
	   }
	   else 
		{
		 $bookpaymentData['amount']=$txnRs['amount'];
		 $tempCod=0;
		}
		
		 
		 if($tempCod!=10)
			  {
                            $Bookingpayment = new  Bookingpayment(); 
                        $Bookingpayment->exchangeArray($bookpaymentData);
                    $this->getEntityManager()->persist($Bookingpayment);
                    $this->getEntityManager()->flush();
			  }
                  ////entry on booking payment end ///  
                 
///////make offline payment//////////
        ///////offline payment  condition //////
   if(($subuserid==1 || $subuserid==6 || $subuserid==4) && $bsession->paymodeoption!=1)
   {
	   
       /////////get balance amount by uid///
       $em = $this->getEntityManager();
                $crdrqueryBuilder = $em->createQueryBuilder();
                $crdrqueryBuilder->add('select', 'ucrdr')
                        ->add('from', '\Finance\Entity\Fcreditdebit ucrdr')
                       ->where(" ucrdr.uId='" . $bsession->uid . "'   ")
					   ->orderBy('ucrdr.cdId', 'DESC')
                        ->getQuery()
                           ;
              $crdrresult = $crdrqueryBuilder->getQuery()->getArrayResult();
			  
              if(!empty($crdrresult))
			  {
               $balance =  $crdrresult[0]['balance'];
			  }
              else
			  {
                 $balance = 0;
			  }
			 
			  if($tempCod!=10)
			  {
					$crpaymentData['uId']= $bsession->uid;
					$crpaymentData['amount']= $bsession->offlinepayamt;                           
					$crpaymentData['referenceNo']= $bsession->recentbookid;
					$crpaymentData['remark']= $bsession->noteformode;
					$crpaymentData['paymentTypeId']= $bsession->offlinepmode;
					$crpaymentData['balance']= $balance+($bsession->offlinepayamt);
					$crpaymentData['tdate']= $bsession->Tdate;
					$crpaymentData['crdrStatus']= 1;
					$crpaymentData['status']= 1;
					
					if($subuserid!=4 && $bsession->paymodeoption==3)
						{
								$newcrdrbalace = $balance+$bsession->offlinepayamt;
								
								$crdrpayment = new  Fcreditdebit(); 
								$crdrpayment->exchangeArray($crpaymentData);
								$this->getEntityManager()->persist($crdrpayment);
								$this->getEntityManager()->flush(); 
								 
								$recentcdId =  $crdrpayment->cdId;
								$updaterefno = $this->getEntityManager()->getRepository('\Finance\Entity\Fcreditdebit')->findOneBy(array('cdId'=>$recentcdId));  
								$updaterefno->referenceNo="CA0000".$recentcdId;
								$this->getEntityManager()->flush(); 
						 }
						 else
						 {
							 $newcrdrbalace = $balance;
						 }
						 
                    ///////////////////////////// Debit //////////////////////////////////////////
                                        if($subuserid==4 && ($balance < $bsession->offlinepayamt) )
                                        {
                                            echo "<script>alert('Low wallet balance..!'); </script>";
                    	  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'booking'
                ));
                                        }
					$crdrpaymentData['uId']= $bsession->uid;
					$crdrpaymentData['amount']= $bsession->offlinepayamt;                           
					$crdrpaymentData['referenceNo']= $bsession->recentbookid;;
					$crdrpaymentData['remark']= $bsession->recentbookid.' Booking Payment';
					$crdrpaymentData['paymentTypeId']= 0;
					$crdrpaymentData['balance']= $newcrdrbalace-$bsession->offlinepayamt;
					$crdrpaymentData['tdate']= $bsession->Tdate;
					$crdrpaymentData['crdrStatus']= 0;
					$crdrpaymentData['status']= 1;
					$crdrpayment = new  Fcreditdebit(); 
					$crdrpayment->exchangeArray($crdrpaymentData);
					$this->getEntityManager()->persist($crdrpayment);
					$this->getEntityManager()->flush(); 
                    
					
				    $recentcdId =  $crdrpayment->cdId;
				    $updaterefno = $this->getEntityManager()->getRepository('\Finance\Entity\Fcreditdebit')->findOneBy(array('cdId'=>$recentcdId));  
					$updaterefno->referenceNo="CA0000".$recentcdId;
					$this->getEntityManager()->flush(); 
			  }
       
   }
   
/////////make offline payment//////////
                    
                    
//                      $redirectUrl='http://www.jaidevcoolcab.com/booking/mbookings';
//                                                            return $this->redirect()->toUrl($redirectUrl);
                   $returnvalue=$this->_checkSession();
				     
                    if($returnvalue=="")
                    {
                    //Start User Login
                   $usremailValu=$psession->email;
                     $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'sup')
                        ->add('from', '\User\Entity\Signup sup')
                        ->where("(sup.email)= '" .  $usremailValu . "'  ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
                
                
                    if(empty($result))
                    {
                        $loginpass=$passwordval;
                    }
                    else
                    {
                        $loginpasseCn =  $result[0]['password'];
                    }
                        $user = new Signin;
                       
                            $user->setPassword($loginpasseCn);
                            $passvalue=$loginpasseCn;

                        $email=$usremailValu;

                        $password=$passvalue;
                        $user = $em->getRepository('Signin\Entity\Signin')->findOneBy(array('email' => $email, 'password' => $password));
                        //print_r(user);
                        if ($user !== null)
                        {
							//print_r(user);
                            $status=$user->status;
//                            if($status==3)
//                            {
//                                $this->flashMessenger()->addErrorMessage('Your account disabled by admin.');
//                                return array('form' => $form,'flashMessages' => $this->flashMessenger()->getErrorMessages());
//                                exit;
//                            }
                          // If you used another name for the authentication service, change it here
                            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                            $adapter = $authService->getAdapter();
                            $adapter->setIdentityValue($email);
                            $adapter->setCredentialValue($password);
                            $authResult = $authService->authenticate();
                            if($authResult->isValid()) {
                                $identity = $authResult->getIdentity();
                                $authService->getStorage()->write($identity);
                                if($travelRootvalue!=12)
                                {
                                 $this->bookingmailer();
                                
                                }
                                 if($travelRootvalue==12)
                                {
                                 $this->bookingmailerdeal();
                                
                                }
                                 $this->smssender(); 
										
                                 $hosturl=$_SERVER['HTTP_HOST'];
                                 $hostarry = explode('.', $hosturl);
								
								///////////////////////////////////// Auto forword to vendor start ////////////////////////////////////////////////////////////
			    
				$em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'shcab')
                        ->add('from', '\Tariff\Entity\Sharecab shcab')
                       ->where(" shcab.vShId='" . $bsession->Vsid . "'   ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
		
				 
				
					if($result[0]['fpId']!=0)
						{
							$dataarr =$this->calVendor($bsession->recentbookid); 
							$this->forwardtovendor($dataarr); 
						}
        
    
								
					 ////////////////////////////////// Auto forword to vendor end /////////////////////////////////////////////////////////////////
					 
								 if($subuserid==1 || $subuserid==6)
   										{
											 $redirectUrl="http://".$hosturl."/booking";
										} 
                                 else if($hostarry[1]==$loginenv)
                                   {
                                      if($loginenv=="cabsaas")
                                        {
                                            $redirectUrl="https://".$hosturl."/booking/mbookings";
                                        }
                                        else
                                        {
                                            $redirectUrl="http://".$hosturl."/booking/mbookings";
                                        }
                                   }
                                   else
                                   {
                                        $redirectUrl="http://".$hosturl."/booking/mbookings";
                                   }    
                                   // $this->url('booking/default', array('controller' =>'finance', 'action' => 'creditnote'));
                                //$redirectUrl='http://www.jaidevcoolcab.com/booking/mbookings';
								return $this->redirect()->toUrl($redirectUrl);
                                       // return $this->redirect()->toRoute('booking', array('action' => 'mbookings' ));
                                   
                            }
                           
                               
                        }
                       
                    //End User Login
                    }
					else
					{
                                if($travelRootvalue!=12)
                                {
								//	$this->bookingmailer();
                                }
                                 if($travelRootvalue==12)
                                {
                             //     $this->bookingmailerdeal(); 
                                }
                                          //  $this->smssender();
                                            $hosturl=$_SERVER['HTTP_HOST'];
                                            $hostarry = explode('.', $hosturl);
											
											
			         ///////////////////////////////////// Auto forword to vendor start ////////////////////////////////////////////////////////////
			    
				$em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'shcab')
                        ->add('from', '\Tariff\Entity\Sharecab shcab')
                       ->where(" shcab.vShId='" . $bsession->Vsid . "'   ")
                        ->getQuery();
                $result = $queryBuilder->getQuery()->getArrayResult();
		
				 
				
        if($result[0]['fpId']!=0)
		{
								 $dataarr =$this->calVendor($bsession->recentbookid); 
								  $this->forwardtovendor($dataarr); 
		}
        
    
								
					 ////////////////////////////////// Auto forword to vendor end /////////////////////////////////////////////////////////////////
								
								
											 if($subuserid==1 || $subuserid==6)
												{
													 $redirectUrl="http://".$hosturl."/booking";
												} 
                               			  else if($hostarry[1]==$loginenv)
                                                {
                                                     if($loginenv=="cabsaas")
                                                        {
                                                        $redirectUrl="https://".$hosturl."/booking/mbookings";
                                                        }
                                                        else
                                                        {
                                                            $redirectUrl="http://".$hosturl."/booking/mbookings";
                                                            
                                                        }
                                                }
                                                else
                                                {
                                                     $redirectUrl="http://".$hosturl."/booking/mbookings";
                                                }    
						 // $redirectUrl='http://www.jaidevcoolcab.com/booking/mbookings';
                                                            return $this->redirect()->toUrl($redirectUrl);
                                        }
					
         ///entry on booking payment////end///
         
        }
        else
        {
          echo "<script>alert('alredy booked by other user!'); </script>";  
           
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'index'
                ));
        }
//            /////success and make  status update////
//        	$order_id=$txnRs['txnid'];  
//			$amount=$txnRs['amount'];  
//                  
//                        $bsession = new \Zend\Session\Container('Booking' );
//       $Vsid= $bsession->Vsid; $noofseats= $bsession->noofseats ;  $genders= $bsession->genders;  
//        $noofseats= ltrim ($noofseats, ',');
//         $noofseatsarr =   explode(',', $noofseats);
//            $gender =   explode(',', $genders);
//          $status='';
//        for($i=0;$i<count($noofseatsarr);$i++)
//        {
//            
//            $status = $status.','.$noofseatsarr[$i].'-'.'1';
//             
//         //$statusChk =    new \Tariff\Entity\Shared();
//            $statusChk = $this->getEntityManager()->getRepository('\Tariff\Entity\Shared')->findOneBy(array('vShId'=>$Vsid,'seatNo'=>$noofseatsarr[$i]) ); 
//            $statusChk->status=1;
//             $statusChk->bookedby=$gender[$i];              
//            $statusChk->processtime= date('Y-m-d h:i:s');
//            $statusChk->currentsession= $bsession->getManager()->getId();
//                    $this->getEntityManager()->flush();     
//       }
//        $statuss= ltrim ($status, ',');
//         $recentbookid =  $bsession->recentbookid;
//        $statusconfirm= $this->getEntityManager()->getRepository('Booking\Entity\Bookingshare')->findOneBy(array('bookingId'=>$recentbookid) ); 
//        $statusconfirm->status=$statuss;
//         $this->getEntityManager()->flush(); 
                        
                        
 	  }
  
     } 
       			else
				   {   //$classObj->redirect($siteUrl); 
					   echo "<script>alert('Error occured please contact support team 1!'); </script>";
					    
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocessfail'
                ));
				   }
	  }
	else
	 {    
             echo "<script>alert('Error occured please contact support team 1!'); </script>";
			  
			  return $this->redirect()->toRoute('website/default', array(
                     'controller' => 'index',                       
                            'action' => 'paymentprocessfail'
                ));
     }
         
  }
  
  
    public function paymentprocessfailAction()
     { $this->getRealUrl(); }
  
    public function goshareAction()
    {
        $loginenv=$this->getenv();
          $Vsid = filter_input(INPUT_POST, 'vshid');
             $Trip = filter_input(INPUT_POST, 'sharetrip');
              $Tdate = filter_input(INPUT_POST, 'Tdate');
               $droptext = filter_input(INPUT_POST, 'droptext');
                $pickuptext = filter_input(INPUT_POST, 'pickuptext');                
                $PCity = filter_input(INPUT_POST, 'PCity');
                $DCity = filter_input(INPUT_POST, 'DCity');   
                $vid = filter_input(INPUT_POST, 'vid'); 
                $drop = filter_input(INPUT_POST, 'drop');
                $pickup = filter_input(INPUT_POST, 'pickup');
               $noofseats = filter_input(INPUT_POST, 'noofseats');
            $noofseats= ltrim ($noofseats, ',');
         $noofseatsarr =   explode(',', $noofseats);
          
         $bsession = new \Zend\Session\Container('Booking' );
             // echo $bsession->getManager()->getId();   
             ////  mpd6l9237l2q2pld25jlhngg22true;
           $bsession->Vsid = $Vsid; $bsession->Trip = $Trip;$bsession->noofseats = $noofseats;$bsession->Tdate = $Tdate;$bsession->droptext = $droptext;$bsession->pickuptext = $pickuptext;$bsession->PCity = $PCity;$bsession->DCity = $DCity;$bsession->vid = $vid;
           $bsession->drop = $drop;$bsession->pickup = $pickup;
         
            $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder(); 
		
		////////ckeck already seat in process ////
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $notempty=0; $empty=0;
        for($i=0;$i<count($noofseatsarr);$i++)
          {
            $queryBuilder->add('select', 'shaed')
                    ->add('from', '\Tariff\Entity\Shared shaed') 
                     ->where("shaed.vShId =  " . $Vsid . " AND shaed.seatNo =" . $noofseatsarr[$i] . " AND shaed.tdate =  '" . $Tdate . "'   ");
                      $resultcsession = $queryBuilder->getQuery()->getArrayResult();
                      
                      if(empty($resultcsession[0]['currentsession']))
                      {
                     // echo "empty";
                      $empty =1;
                      
                       }
                       else
                       { 
                           $notempty =2;//echo "not empty";
                           $queryBuildercs = $em->createQueryBuilder();
                            $queryBuildercs->add('select', 'shaed')
                    ->add('from', '\Tariff\Entity\Shared shaed') 
                     ->where("shaed.vShId =  " . $Vsid . " AND shaed.seatNo = " . $noofseatsarr[$i] . " AND shaed.tdate =  '" . $Tdate . "' AND shaed.currentsession =  '" . $bsession->getManager()->getId() . "'   ");
                     $resultcsessionnotempty = $queryBuildercs->getQuery()->getArrayResult();       
                     if(empty($resultcsession[0]['currentsession']))
                     {
                          $result = false;
         echo json_encode($result);
       exit; 
                     }
                       }
                       
        }
        
        if($notempty==0)
        {
          //  echo "cool";
               for($i=0;$i<count($noofseatsarr);$i++)
        {
         //$statusChk =    new \Tariff\Entity\Shared();
            $statusChk = $this->getEntityManager()->getRepository('\Tariff\Entity\Shared')->findOneBy(array('vShId'=>$Vsid,'seatNo'=>$noofseatsarr[$i],'tdate'=>$Tdate) ); 
            $statusChk->status=3;
            $statusChk->processtime= date('Y-m-d h:i:s');
            $statusChk->currentsession= $bsession->getManager()->getId();
                    $this->getEntityManager()->flush();     
       }
       $result = true;
         echo json_encode($result);
       exit; 
            
        }
        else
        {
            $result = false;
         echo json_encode($result);
       exit; 
        }
                      
      //  print_r($resultcsession);
        //die;
        ////////ckeck already seat in process end/////
		
		//
//      //  print_r($noofseatsarr); 
//        for($i=0;$i<count($noofseatsarr);$i++) 
//        {
//         //$statusChk =    new \Tariff\Entity\Shared();
//            $statusChk = $this->getEntityManager()->getRepository('\Tariff\Entity\Shared')->findOneBy(array('vShId'=>$Vsid,'seatNo'=>$noofseatsarr[$i],'tdate'=>$Tdate) ); 
//            $statusChk->status=3;
//            $statusChk->processtime= date('Y-m-d h:i:s');
//            $statusChk->currentsession= $bsession->getManager()->getId();
//                    $this->getEntityManager()->flush();     
//       }
//                    
//             
//           $result = true;
//         echo json_encode($result);
//       exit; 
    } 
	
	
    
function getroundtripvalues($cityId,$dcityid,$uid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();         
            $queryBuilder->add('select', 'v.vehicleName as vehicle,ct.ctname as sourceCity,dct.ctname as destinationCity,v.vehicleSeatCapacityNo as  seatingCapacity,tr.vehicleId,tr.perKmRate as perKm ,tr.minAvgPerDay as MinimumChargedDistance,tr.drivAllPerDay as driverCharges,tr.nightHalt,tr.nightCharges')
                    ->add('from', '\Tariff\Entity\Roundtrip tr')
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $cityId.' ')
                     ->innerJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= '. $dcityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  tr.vehicleId ')
                      ->where("tr.cityId =  '" . $cityId . "' AND tr.uId='" . $uid . "'");
               $result = $queryBuilder->getQuery()->getArrayResult();
           return $result;
    } 
 
   function gethillcharges($outstationtypeid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();        
            $queryBuilder->add('select', 'v.vehicleName,hc.hillCharge,hc.stateCharge,hc.otherCharge')
                    ->add('from', '\Tariff\Entity\Hillcharges hc')
                     ->innerJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId= '. $vid.' ')
                    ->where("hc.outstationTypeId =  '" . $outstationtypeid . "'AND hc.vehicleId =  " . $vid . " ");
                     $result = $queryBuilder->getQuery()->getArrayResult();
       return $result ;        
    }
    
    
      function getapproximatedistance($pCity,$dCity) {        
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->add('select', 'o')
                    ->add('from', '\Tariff\Entity\Outstationtype o') 
                     ->where("o.source =  " . $pCity . " AND o.destination =  " . $dCity . " ");
                      $result = $queryBuilder->getQuery()->getArrayResult();
                      
               return $result;
    }
    
    function getcitynameAction() {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();  
        $cid = filter_input(INPUT_POST, 'City');
            $queryBuilder->add('select', 'ct.ctname')
                    ->add('from', '\Admin\Entity\City ct')
                    ->where(" ct.cityId =  '" .  ($cid) . "'  ");
                    $result = $queryBuilder->getQuery()->getArrayResult();
                 //   print_r($result);die;
         
         echo json_encode($result);
       exit;
    }  
    
       public function getonewayvalues($cityId,$dcityid,$uid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

            $queryBuilder->add('select',
                    'v.vehicleName as vehicle,ct.ctname as sourceCity,dct.ctname as destinationCity,v.vehicleSeatCapacityNo as seatingCapacity,ow.vehicleId,ow.perKmRate as perKm,ow.oneWayPerKmRate,ow.minAvgPerDay,ow.drivAllPerDay,ow.nightHalt,ow.nightCharges')
                    ->add('from', '\Tariff\Entity\Oneway ow')
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $cityId.' ')
                     ->innerJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= '. $dcityid.' ')
                    ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  ow.vehicleId ')
                    ->where("ow.cityId =  '" . $cityId . "' AND  ow.uId='" . $uid . "'");
                    
        $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
    }
    
    public function getmulticityapproximatedistance($pCity,$dCity,$noOfDays) {
          //$postedData = $this->params()->fromQuery();
         $totDist = 0;
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $st = 0;
        $hl = 0;
        $tc = 0;
        $str = "";  
        $uid=0;
         $dCityArr = $dCity;  
         $mainarraygettripvalues=array();
       $noOfDays =  ($noOfDays);  
        // $serviceTax = '5.6'; 
        $serviceTax = '0'; 
         $flag=0;
         array_unshift($dCityArr, $pCity);
      //print_r($dCityArr);die;
         if ($dCityArr[sizeof($dCityArr) - 1] != $pCity) {
            $dCityArr[] = $pCity;
        }
        //print_r($dCityArr);die;
        for ($city = 0; $city < sizeof($dCityArr) - 1; $city++)
        {
           $Outstationtypeid =$this->getapproximatedistance($dCityArr[$city],$dCityArr[$city + 1]);
            if(!empty($Outstationtypeid))
            {
            
           $outstationid = $Outstationtypeid[0]['outstationtypeId'];
           $approx = $Outstationtypeid[0]['distance'];
           $approx = $approx/2;
            }
 else {return $mainarraygettripvalues=''    ;   }
           
           //get data fron source city to first inner city start//
       if($flag==0){    
///////////get getroundtripvalues  //////////////       
    $getroundtripvalues = $this->getroundtripvalues($dCityArr[$city],$dCityArr[$city + 1],$uid);
      ///////////end get getroundtripvalues  //////////////
             
    for($i=0; $i<count($getroundtripvalues);$i++)
    {
         $hillcharges =  $this->gethillcharges($outstationid,$getroundtripvalues[$i]['vehicleId']);
         ///////////get hill charges /get multipale value////
                  
         if(!empty($hillcharges))
          {           
        $getroundtripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getroundtripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getroundtripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getroundtripvalues[$i]['hillCharge']= 0  ;
        $getroundtripvalues[$i]['stateCharge']= 0  ;
        $getroundtripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getroundtripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
     
        //////////fare calculation///////
                 
       $flag=1;  
    }
       }
       //get data fron source city to first inner city end//
       
       
       ////start get inner city hill data and add with $mainarraygettripvalues array //
   if($city>0)
   {
          
       for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
       $hillcharges =  $this->gethillcharges($outstationid,$mainarraygettripvalues[$i]['vehicleId']);
      
       if(!empty($hillcharges))
          {           
        $mainarraygettripvalues[$i]['hillCharge']=     $mainarraygettripvalues[$i]['hillCharge']+$hillcharges[0]['hillCharge'];
       $mainarraygettripvalues[$i]['stateCharge']=     $mainarraygettripvalues[$i]['stateCharge']+$hillcharges[0]['stateCharge'];
       $mainarraygettripvalues[$i]['otherCharge']=     $mainarraygettripvalues[$i]['otherCharge']+$hillcharges[0]['otherCharge'];
           }
          else  
          {           
             $mainarraygettripvalues[$i]['hillCharge']=     $mainarraygettripvalues[$i]['hillCharge']+0;
       $mainarraygettripvalues[$i]['stateCharge']=     $mainarraygettripvalues[$i]['stateCharge']+0;
       $mainarraygettripvalues[$i]['otherCharge']=     $mainarraygettripvalues[$i]['otherCharge']+0;
              }  
              $mainarraygettripvalues[$i]['ApproxDistance']= $mainarraygettripvalues[$i]['ApproxDistance']+$approx;
            }  
   }   
   ////get inner city hill data and add with $mainarraygettripvalues array end //
              }       
          return $mainarraygettripvalues;                   
    }
    
    
    
    
        public function getfulldayvalues($scityid,$uid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
$queryBuilder->add('select', 'fd.vehicleId,v.vehicleName as vehicle,v.vehicleSeatCapacityNo as  seatingCapacity,ct.ctname as sourceCity,fd.perKmRate as perKm,fd.perHourRate,fd.fixHoursPerDay,fd.minKmsPerDay,fd.basicFare as localBasicRate,fd.nightCharges'                  )
                         ->add('from', '\Tariff\Entity\Fullday fd')                      
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $scityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  fd.vehicleId ')         
                    ->where("fd.cityId =  '" . $scityid . "' AND  fd.uId='" . $uid . "'")
                     ;
       
        $result = $queryBuilder->getQuery()->getArrayResult();
         
        return $result;
         
    }
      public function gethalfdayvalues($scityid,$uid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
         
            $queryBuilder->add('select', 'hd.vehicleId,v.vehicleName as vehicle,v.vehicleSeatCapacityNo as  seatingCapacity,ct.ctname as sourceCity, hd.perKmRate as perKm, hd.perHourRate,hd.fixHoursPerDay,hd.minKmsPerDay, hd.basicFare as localBasicRate, hd.nightCharges')
                    ->add('from', '\Tariff\Entity\Halfday hd')
                    ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $scityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  hd.vehicleId ')   
                    ->where("hd.cityId =  '" . $scityid . "' AND   hd.uId='" . $uid . "'")
                    ;
        
        $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
         
    } 
    
    
    public function getlocationAction() {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

        $pCity = filter_input(INPUT_POST, 'Cityid');
             $queryBuilder->add('select', 'l')
                    ->add('from', '\Tariff\Entity\Location l')
                    ->where("l.cityId =  '" . $pCity . "' AND l.airportRailwayStatus = '" . 1 . "'");
                    //->orderBy('l.airportRailwayStatus', 'DESC');
        
        $apresult = $queryBuilder->getQuery()->getArrayResult();
        
         $queryBuilder->add('select', 'l')
                    ->add('from', '\Tariff\Entity\Location l')
                    ->where("l.cityId =  '" . $pCity . "' AND l.airportRailwayStatus = '" . 2 . "'");
                   // ->orderBy('l.airportRailwayStatus', 'DESC');
        
        $rwresult = $queryBuilder->getQuery()->getArrayResult();
         $queryBuilder->add('select', 'l')
                    ->add('from', '\Tariff\Entity\Location l')
                    ->where("l.cityId =  '" . $pCity . "'  ");
                    //->orderBy('l.airportRailwayStatus', 'DESC');
        
        $fulllocresult = $queryBuilder->getQuery()->getArrayResult();
        $result = array( 'fulllocresult' => $fulllocresult, 'rwresult' => $rwresult , 'apresult' => $apresult );
        echo json_encode($result);
        exit;
    }
    
        function getdistance($pl,$dl,$cid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();         
            $queryBuilder->add('select', 'to.locDistance')
                    ->add('from', '\Admin\Entity\Transferoutstation to')
                    ->where("to.locSourceId =  '" . $pl . "' AND to.locDestination='" . $dl . "' AND to.locCityId='" . $cid . "'");
                    $result = $queryBuilder->getQuery()->getArrayResult();
         
                    return $result ;
    
        }
        
              function gettransferrate($cid,$uid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();         
            $queryBuilder->add('select', 't.vehicleId,v.vehicleName as vehicle ,v.vehicleSeatCapacityNo as seatingCapacity,t.amountFor10 as FareUpto10km,t.amountFor20 as FareUpto20km,t.amountFor40 as FareUpto40km,t.amountFor60 as FareUpto60km,t.amountFor80 as FareUpto80km,t.amountFor100 as  FareAbove100km,t.waitingChargesPerHour as waitingHourRate')
                    ->add('from', '\Tariff\Entity\Transfer t')
                    ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  t.vehicleId ')
                    ->where("t.cityId =  '" . $cid . "' AND t.uId='" . $uid . "'  ");
                    $result = $queryBuilder->getQuery()->getArrayResult();
        
                    return $result ;
    
        }
        
        function getroundtripvaluesforbooking($cityId,$dcityid,$uid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();         
            $queryBuilder->add('select', 'v.vehicleName,ct.ctname as SourceCity,dct.ctname as DestinationCity,v.vehicleSeatCapacityNo as vehicleSeatCapacity,tr.vehicleId,tr.perKmRate,tr.minAvgPerDay as MinimumChargedDistance,tr.drivAllPerDay as driverCharges,tr.nightHalt,tr.nightCharges')
                    ->add('from', '\Tariff\Entity\Roundtrip tr')
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $cityId.' ')
                     ->innerJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= '. $dcityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  tr.vehicleId ')
                      ->where("tr.cityId =  '" . $cityId . "' AND tr.uId='" . $uid . "' AND    tr.vehicleId ='" . $vid . "' ");
               $result = $queryBuilder->getQuery()->getArrayResult();
           return $result;
    }
    
     public function getonewayvaluesforbooking($cityId,$dcityid,$uid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

            $queryBuilder->add('select',
                    'v.vehicleName,ct.ctname as SourceCity,dct.ctname as DestinationCity,v.vehicleSeatCapacityNo as vehicleSeatCapacity,ow.vehicleId,ow.perKmRate,ow.oneWayPerKmRate,ow.minAvgPerDay,ow.drivAllPerDay,ow.nightHalt,ow.nightCharges')
                    ->add('from', '\Tariff\Entity\Oneway ow')
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $cityId.' ')
                     ->innerJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= '. $dcityid.' ')
                    ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  ow.vehicleId ')
                    ->where("ow.cityId =  '" . $cityId . "' AND  ow.uId='" . $uid . "'AND    ow.vehicleId ='" . $vid . "'");
                    
        $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
    }
    
      public function getmulticityapproximatedistanceforbooking($pCity,$dCity,$noOfDays,$Vid) {
          //$postedData = $this->params()->fromQuery();
         $totDist = 0;
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $st = 0;
        $hl = 0;
        $tc = 0;
        $str = "";  
        $uid=0;
         $dCityArr = $dCity;  
         $mainarraygettripvalues=array();
       $noOfDays =  ($noOfDays);  
         //$serviceTax = '5.6'; 
        $serviceTax = '0'; 
         $flag=0;
         array_unshift($dCityArr, $pCity);
       // print_r($dCityArr);die;
         if ($dCityArr[sizeof($dCityArr) - 1] != $pCity) {
            $dCityArr[] = $pCity;
        }
      // print_r($dCityArr);die;
        for ($city = 0; $city < sizeof($dCityArr) - 1; $city++)
        {
           $Outstationtypeid =$this->getapproximatedistance($dCityArr[$city],$dCityArr[$city + 1]);
            if(!empty($Outstationtypeid))
            {
            
           $outstationid = $Outstationtypeid[0]['outstationtypeId'];
           $approx = $Outstationtypeid[0]['distance'];
           $approx = $approx/2;
            }
 else {return $mainarraygettripvalues=''    ;   }
           
           //get data fron source city to first inner city start//
       if($flag==0){    
///////////get getroundtripvalues  //////////////       
    $getroundtripvalues = $this->getroundtripvaluesforbooking($dCityArr[$city],$dCityArr[$city + 1],$uid,$Vid);
      ///////////end get getroundtripvalues  //////////////
             
    for($i=0; $i<count($getroundtripvalues);$i++)
    {
         $hillcharges =  $this->gethillcharges($outstationid,$getroundtripvalues[$i]['vehicleId']);
         ///////////get hill charges /get multipale value////
                  
         if(!empty($hillcharges))
          {           
        $getroundtripvalues[$i]['hillCharge']= $hillcharges[0]['hillCharge']  ;
        $getroundtripvalues[$i]['stateCharge']= $hillcharges[0]['stateCharge']  ;
        $getroundtripvalues[$i]['otherCharge']= $hillcharges[0]['otherCharge']  ;
           }
          else  
          {           
           $getroundtripvalues[$i]['hillCharge']= 0  ;
        $getroundtripvalues[$i]['stateCharge']= 0  ;
        $getroundtripvalues[$i]['otherCharge']= 0  ;
          }
        
          array_push($mainarraygettripvalues,$getroundtripvalues[$i]);
        
        $mainarraygettripvalues[$i]['days'] = $noOfDays;
        $mainarraygettripvalues[$i]['ApproxDistance'] = $approx;
        $mainarraygettripvalues[$i]['ServiceTax'] = $serviceTax;
        $mainarraygettripvalues[$i]['ApiDiscount'] = 'Need to calulate';
     
        //////////fare calculation///////
                 
       $flag=1;  
    }
       }
       //get data fron source city to first inner city end//
       
       
       ////start get inner city hill data and add with $mainarraygettripvalues array //
   if($city>0)
   {
          
       for($i=0; $i<count($mainarraygettripvalues);$i++)
    {
       $hillcharges =  $this->gethillcharges($outstationid,$mainarraygettripvalues[$i]['vehicleId']);
      
       if(!empty($hillcharges))
          {           
        $mainarraygettripvalues[$i]['hillCharge']=     $mainarraygettripvalues[$i]['hillCharge']+$hillcharges[0]['hillCharge'];
       $mainarraygettripvalues[$i]['stateCharge']=     $mainarraygettripvalues[$i]['stateCharge']+$hillcharges[0]['stateCharge'];
       $mainarraygettripvalues[$i]['otherCharge']=     $mainarraygettripvalues[$i]['otherCharge']+$hillcharges[0]['otherCharge'];
           }
          else  
          {           
             $mainarraygettripvalues[$i]['hillCharge']=     $mainarraygettripvalues[$i]['hillCharge']+0;
       $mainarraygettripvalues[$i]['stateCharge']=     $mainarraygettripvalues[$i]['stateCharge']+0;
       $mainarraygettripvalues[$i]['otherCharge']=     $mainarraygettripvalues[$i]['otherCharge']+0;
              }  
              $mainarraygettripvalues[$i]['ApproxDistance']= $mainarraygettripvalues[$i]['ApproxDistance']+$approx;
            }  
   }   
   ////get inner city hill data and add with $mainarraygettripvalues array end //
              }       
          return $mainarraygettripvalues;                   
    }
    
    public function getfulldayvaluesforbooking($scityid,$uid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
$queryBuilder->add('select', 'fd.vehicleId,v.vehicleName,v.vehicleSeatCapacityNo as vehicleSeatCapacity,ct.ctname as SourceCity,fd.perKmRate,fd.perHourRate,fd.fixHoursPerDay,fd.minKmsPerDay,fd.basicFare,fd.nightCharges'                  )
                         ->add('from', '\Tariff\Entity\Fullday fd')                      
                     ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $scityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  fd.vehicleId ')         
                    ->where("fd.cityId =  '" . $scityid . "' AND   fd.uId='" . $uid . "' AND  fd.vehicleId ='" . $vid . "'")
                     ;
       
        $result = $queryBuilder->getQuery()->getArrayResult();
         
        return $result;
         
    }
    
     public function gethalfdayvaluesforbooking($scityid,$uid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 'hd.vehicleId,v.vehicleName,v.vehicleSeatCapacityNo as vehicleSeatCapacity,ct.ctname as SourceCity, hd.perKmRate, hd.perHourRate,hd.fixHoursPerDay,hd.minKmsPerDay, hd.basicFare, hd.nightCharges')
                    ->add('from', '\Tariff\Entity\Halfday hd')
                    ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $scityid.' ')
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  hd.vehicleId ')   
                    ->where("hd.cityId =  '" . $scityid . "' AND   hd.uId='" . $uid . "' AND   hd.vehicleId ='" . $vid . "'")
                    ;
       
        $result = $queryBuilder->getQuery()->getArrayResult();
         
        return $result;
         
    }
     function gettransferrateforbooking($cid,$uid,$vid) {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();         
            $queryBuilder->add('select', 't.vehicleId,v.vehicleName,v.vehicleSeatCapacityNo as vehicleSeatCapacity,t.amountFor10 as FareUpto10km,t.amountFor20 as FareUpto20km,t.amountFor40 as FareUpto40km,t.amountFor60 as FareUpto60km,t.amountFor80 as FareUpto80km,t.amountFor100 as  FareAbove100km,t.waitingChargesPerHour')
                    ->add('from', '\Tariff\Entity\Transfer t')
                    ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  t.vehicleId ')
                    ->where("t.cityId =  '" . $cid . "' AND t.uId='" . $uid . "'AND   t.vehicleId='" . $vid . "'  ");
                    $result = $queryBuilder->getQuery()->getArrayResult();
        
                    return $result ;
    
        }
        
       function getcab($pcity,$dcity,$tDate)
       {
             
           $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 'vs,v.vehicleName,slot.time,v.vehicleSeatCapacityNo as vehicleSeatCapacity,v.vPhoto,ct.ctname as SourceCity,dct.ctname as DestinationCity')
                    ->add('from', '\Tariff\Entity\Sharecab vs')
                    ->innerJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= '. $pcity.' ')
          ->innerJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= '. $dcity.' ')
         
 ->leftJoin('\Admin\Entity\TimeSlot', 'slot',\Doctrine\ORM\Query\Expr\Join::WITH,
              'slot.slotId=  vs.slotId ')
                
         
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  vs.vehicleId ')  
         
         
          
         
                    ->where("vs.sCityId =  '" . $pcity . "' AND      vs.dCityId ='" . $dcity . "' AND vs.uId =2");
       
        $result = $queryBuilder->getQuery()->getArrayResult();
          
        return $result;
           
       }
       
       
      function getseatfare($vShId,$tDate,$slotid)
      {
            
          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 'ts')
                    ->add('from', '\Admin\Entity\TimeSlot ts')
         ->where("ts.slotId =  '" . $slotid . "'  ")       ;
 $result = $queryBuilder->getQuery()->getArrayResult();
      
 
           ($result[0]['time']) ;
          "</br  >";
                $totime =     (date('H:i:s', time()));
              
                $todate =  (date('Y-m-d')); 
               
                 $tDate =  ($tDate);
               
               if($todate<$tDate  )
               {
          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 's')
                    ->add('from', '\Tariff\Entity\Shared s')
         ->where("s.vShId =  '" . $vShId . "' AND s.tdate=  '" . $tDate . "' ")       ;
 $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
               }
  
               if($todate==$tDate && $totime < ($result[0]['time']))
               {
          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 's')
                    ->add('from', '\Tariff\Entity\Shared s')
         ->where("s.vShId =  '" . $vShId . "' AND s.tdate=  '" . $tDate . "' ")       ;
 $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
               }
              // print_r($result);die;
     
      }
      
        function getsinglecab($vsid)
       {
           $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
 $queryBuilder->add('select', 'vs,v.vehicleName,slot.time,v.vehicleSeatCapacityNo as vehicleSeatCapacity,v.vPhoto,ct.ctname as SourceCity,dct.ctname as DestinationCity')
                    ->add('from', '\Tariff\Entity\Sharecab vs')
                    ->leftJoin('\Admin\Entity\City', 'ct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'ct.cityId= vs.sCityId')
          ->leftJoin('\Admin\Entity\City', 'dct',\Doctrine\ORM\Query\Expr\Join::WITH,
            'dct.cityId= vs.dCityId ')
         
 ->leftJoin('\Admin\Entity\TimeSlot', 'slot',\Doctrine\ORM\Query\Expr\Join::WITH,
             'slot.slotId=  vs.slotId ')
         
                      ->leftJoin('\Admin\Entity\Vehicle', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,
             'v.vehicleId=  vs.vehicleId ')   
                    ->where("vs.vShId =  '" . $vsid . "'   ")
                    ;
       
        $result = $queryBuilder->getQuery()->getArrayResult();
          
        return $result;
           
       }
      
     function  getpicdroplocAction()
     {
         $Vsid = filter_input(INPUT_POST, 'id');
         
          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder(); 
        
         $queryBuilder->add('select', 'pdloc, pkloc.locname as locationName')
                    ->add('from', '\Tariff\Entity\Pickdroploc pdloc')
                    ->leftJoin('\Admin\Entity\Location', 'pkloc',\Doctrine\ORM\Query\Expr\Join::WITH,
            'pkloc.locationId= pdloc.locId ')
                ->where("pdloc.vShId =  '" . $Vsid . "' and  pdloc.status =  0   ");
             
        $picresult = $queryBuilder->getQuery()->getArrayResult();
        
        $queryBuilder->add('select', 'pdloc, drloc.locname as locationName')
                    ->add('from', '\Tariff\Entity\Pickdroploc pdloc')
                    ->leftJoin('\Admin\Entity\Location', 'drloc',\Doctrine\ORM\Query\Expr\Join::WITH,
            'drloc.locationId= pdloc.locId ')
                ->where("pdloc.vShId =  '" . $Vsid . "' and  pdloc.status =  1   ");
             
        $dropresult = $queryBuilder->getQuery()->getArrayResult();
        
        
         
        
        $result = array( 'picresult' => $picresult, 'dropresult' => $dropresult  );
        echo json_encode($result);
        exit;
        
        
         
         
     }
       
    public function crngen($countId, $traveRootId) {


        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder->add('select', 'tf')
                ->add('from', '\Admin\Entity\TarriffSubList tf')
                ->where("tf.tariffSListId =" . $traveRootId);

        $result = $queryBuilder->getQuery()->getArrayResult();
        $traveRoot = ($result[0]['shortName'] != "") ? $result[0]['shortName'] : "CL";

        $AlphaArrMon["01"] = "L";
        $AlphaArrMon["02"] = "W";
        $AlphaArrMon["03"] = "V";
        $AlphaArrMon["04"] = "A";
        $AlphaArrMon["05"] = "N";
        $AlphaArrMon["06"] = "X";
        $AlphaArrMon["07"] = "Y";
        $AlphaArrMon["08"] = "E";
        $AlphaArrMon["09"] = "J";
        $AlphaArrMon["10"] = "G";
        $AlphaArrMon["11"] = "D";
        $AlphaArrMon["12"] = "I";


        $AlphaArrYr1 = array("Q", "M", "W", "N", "E", "B", "R", "V", "T", "C", "Y", "X", "U", "Z", "I", "A", "O", "S", "P", "D", "L", "F", "K", "G", "J", "H");
        $j = 0;
        for ($i = 15; $i <= 40; $i++) {
            $AlphaArrYr[$i] = $AlphaArrYr1[$j];
            $j++;
        }



        $tomorrow = mktime(0, 0, 0, date("m"), date("d"), date("y"));
        $y = date('y', $tomorrow);
        $m = date('m', $tomorrow);
        $d = date('d', $tomorrow);




        $d1 = substr($d, 0, 1);
        $d2 = substr($d, 1, 1);

        $crnTemp = $traveRoot . $d1 . $AlphaArrYr[$y] . $AlphaArrMon[$m] . $countId . $d2;
        //echo "<tr><td>$AlphaArrYr[$y]</td><td>$AlphaArrMon[$m]</td><td>$i</td><td>$d1-$d2</td><td>RT$d1$AlphaArrYr[$y]$AlphaArrMon[$m]$i$d2</td></tr>";
        return $crnTemp;
        die;
    } 
      final private function _checkIfUserIsLoggedIn() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
            $userId = $authService->getIdentity()->uId;
            $parentId = $authService->getIdentity()->parentId;
            $subuserid = $authService->getIdentity()->subuserid;
            $subuseridarr = array("4", "6", "7", "8", "10");

            if (!in_array($subuserid, $subuseridarr))
              {
                 if ($parentId != 0 && $parentId != 1) {
                    $success = $em->getRepository('User\Entity\Company')->findOneByuserid($parentId);
                } else if ($parentId == 1 || $parentId == 0 ) {
                    $success = $em->getRepository('User\Entity\Company')->findOneByuserid($userId);
                }
            }
            else
            {
                $success = $em->getRepository('User\Entity\Company')->findOneByuserid($parentId);
            }
            $this->layout()->setVariable('user', $authService->getIdentity());



            $this->layout()->setVariable('company', $success);
        } else {
            //$this->flashMessenger()->clearMessagesFromContainer();
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
    }







    private function _userIdentityValue() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
            return $userId = $authService->getIdentity();
        } else {
            //$this->flashMessenger()->clearMessagesFromContainer();
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
    }
       private function _checkSession() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
           $this->layout()->setVariable('user', $authService->getIdentity());
           return 1;
        } 
    }
       
    ////get diccount ajax//
    
    function getdiscountAction(){
     //  $session=new Container('Discount');
//
       // $sessionc=new Container('Conditions');
        //$sessionc->fromct=1;
        //$sessionc->toct=1;
       // $sessionc->bookingdate=date('Y-m-d');
       // $sessionc->amount=1000;
        
        
   $ans=array();
  // $seats=2;
   $code= filter_input(INPUT_POST, 'pcodetxt');
   $postmode=filter_input(INPUT_POST, 'postmode');
 $postbasic=filter_input(INPUT_POST, 'postbasic');
 $postst=filter_input(INPUT_POST, 'postst');
$postsc=filter_input(INPUT_POST, 'postsc');
$postad=filter_input(INPUT_POST, 'postadv');
$postbal=filter_input(INPUT_POST, 'postbal');  
$postcar=filter_input(INPUT_POST, 'postcar');
$trip=filter_input(INPUT_POST, 'posttrip');
$sessionp=new Container('Before');
$session=new Container('Discount');
$sessionp->postmode=$postmode;
$sessionp->postbasic=$postbasic;
$sessionp->postst=$postst;
$sessionp->postsc=$postsc;
$sessionp->postad=$postad;
$sessionp->postbal=$postbal;

//$postcar=filter_input(INPUT_POST, 'postcar');
//$trip=filter_input(INPUT_POST, 'posttrip');

$basicamount=$session->Amountpay;
//echo $basicamount;die;
  
$session->DisBasicAmt=$session->Amountpay;
$session->offsetUnset('Amountpay');

//echo $basicamount;die;
//$session->offsetUnset('seat');
//echo $basicamount;die;
//$servicetax=round($basicamount*5.6/100);
 $servicetax = '0'; 
$totalamount=$basicamount+$servicetax+$postsc;
 //amecho $totalamount;die;
if($postmode==2){
      $advance = round($basicamount*20/100);
     $Balance =$totalamount - $advance;
    }
 else {
     $advance = $totalamount;   
      $Balance =0;
 }
 if($advance==0)
     $paying=$totalamount;
 else 
   $paying=$advance;
 
//echo "Basic Amount-".$basicamount." ServiceTax:-".$servicetax."State Charges:".$postsc." Total Amount:-".$totalamount ." Advance:-".$advance."Balance Amount".$Balance." You Are PAying:-".$advance;
  $session->code=$code;
 $session->BasicAmount=$basicamount;
 //echo $session->BasicAmount;die;
  $session->ServiceTax=$servicetax;
  $session->StateCharge=$postsc;
 $session->TotalAmount=$totalamount;
 $session->Advance=$advance;
 $session->Balance=$Balance;
 $session->Paying=$advance;
 $ans = array('basicamount'=> $basicamount);
 $ans['ServiceTax']=$servicetax;
 $ans['StateCharge']=$postsc;
$ans['TotalAmount']=$totalamount;
$ans['Advance']=$advance;
$ans['Balance']= $Balance;
$ans['Paying']=$paying;


 echo json_encode($ans);
//echo "Envoking Session".$session->BasicAmount;die; 
 

 exit;
    }
    
    
     public function removediscountAction()
    {
    
        //$session->code=$code;
$session=new Container('Discount');
$sessionp=new Container('Before')   ;
//$servicetax//=$sessionp->postmode;
$seat=$session->seat;
//echo "Before".$seat;die;
$basicamount=$sessionp->postbasic;
$servicetax=$sessionp->postst;
$postsc=$sessionp->postsc;
$advance=$sessionp->postad;
$Balance=$sessionp->postbal;
 $session->BasicAmount=$basicamount;
  $session->ServiceTax=$servicetax;
  $session->StateCharge=$postsc;
 $session->TotalAmount=$basicamount+$servicetax+$postsc;
 $session->Advance=$advance;
 $session->Balance=$Balance;
 $session->Paying=$advance;
 $ans = array('basicamount'=>  $session->BasicAmount);
 $ans['ServiceTax']=$session->ServiceTax;
 $ans['StateCharge']=$session->StateCharge;
$ans['TotalAmount']=$session->TotalAmount;
$ans['Advance']=$session->Advance;
$ans['Balance']= $session->Balance=$Balance;
$ans['Paying']= $session->Paying;

 $sessiont =new Container('Triggered');
                 $sessiont->triggered=0;
 echo json_encode($ans);
        //.echo "hi";
 //echo "After".$session->seat;   
 exit;
        
    }  
    
    /////get discount fun ajax end/////
	
	
  public function bookingmailer() {
         
        $hosturlCurrnet=$_SERVER['HTTP_HOST'];
        $finalurl=$this->getRealUrl();
        $aws = $this->getServiceLocator()->get('aws');
        $client = $aws->get('ses');  
         
	    $hosturl=$finalurl;
       
	    $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        if($companyentity->logo!="")
        {
            $userCompanyLogo=$companyentity->logo;
        }
        else
        {
            $userCompanyLogo="logo.jpg";
        }
        $logopath='/images/company/clogo/'.$userCompanyLogo;
				if($loginenv=="cabsaas")
				{
				$loginurl = "https://" . $hosturlCurrnet . "/signin";
				$loginurllogo = "https://" . $loginurl1.$logopath;
				}
				else
				{
				$loginurl = "http://" . $hosturlCurrnet . "/signin";
				$loginurllogo = "http://" . $loginurl1.$logopath; 
				}
       // $companynameVal=$userCompanyName;
                    
               //die;     
                    
          
                       $bsession = new \Zend\Session\Container('Booking' );
        $recentbookid = $bsession->recentbookid;
        $recentuidvalue=$bsession->uid;
       //  $recentbookid=7;
         $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bookadd')
                        ->add('from', '\Booking\Entity\Bookingadd bookadd')
                       ->where("bookadd.bookingId='".$recentbookid."'")
                        ->getQuery();
                $resultBking = $queryBuilder->getQuery()->getArrayResult();
                //print_r($resultBking);die;
                
                   $recentbookid =  $resultBking[0]['bookingId'];
                    $genrefno =  $resultBking[0]['refNo'];
                     $tDate =  $resultBking[0]['tDate'];
                    $vehicleid = $resultBking[0]['vehicleId'];
                     $pCity =  $resultBking[0]['pCity'];
                     $dCity =  $resultBking[0]['dCity'];
                     $pickupTime =  $resultBking[0]['pickupTime'];
                     $typeofservice = $resultBking[0]['travelType'];
                      $pickupdateTime = date("d-M-y h:i A", strtotime($resultBking[0]['tDate']." ".$resultBking[0]['pickupTime']));
                      
                      $cityarr = $em->getRepository('Admin\Entity\City')->find($pCity);
                    $pCityName = $cityarr->ctname;
                      $cityarr = $em->getRepository('Admin\Entity\City')->find($dCity);
                    $dCityName = $cityarr->ctname;
                    $vehicleyarr = $em->getRepository('Admin\Entity\Vehicle')->find($vehicleid);
                    $vehicleName = $vehicleyarr->vehicleName;
                // die; 
                    //Booking Traveller
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bt')
                        ->add('from', '\Booking\Entity\Bookingtraveller bt')
                       ->where("bt.bookingId='".$recentbookid."' AND bt.uId=".$recentuidvalue)
                        ->getQuery();
                $resultTrler = $queryBuilder->getQuery()->getArrayResult();
                    
                 $travellerName =  $resultTrler[0]['travellerName'];
                   // $travellerEmailId =  $resultTrler[0]['travellerEmailId'];   
                     $travellerContactNo =  $resultTrler[0]['travellerContactNo'];   
                    
                     // $uId =  $resultTrler[0]['uId']; 
                       $location =  $resultTrler[0]['location']; 
                       
                      $userrarr = $em->getRepository('User\Entity\Signup')->find($recentuidvalue);
                    $travellerEmailId = $userrarr->email; 
                       
                    $emailidVal = $travellerEmailId;   
                       
                       
                      
        
            
              //TimeSlotId
          
            
             $splitime=explode(" ",$pickupTime);
                    $splitime2=explode(":",$splitime[0]);
                    $ampm=$splitime[1];
                    if($ampm=="am")
                    {
                        $hours=$splitime2[0];
                        $minuts=$splitime2[1];
                        $totaltime=$hours.":".$minuts.":00";
                    }
                    else
                    {
                        $hours=$splitime2[0];
                        $hours2=$hours+12;
                        $minuts=$splitime2[1];
                        $totaltime=$hours.":".$minuts.":00";
                    }
                 //   echo $totaltime;
                    
                    $queryBuilder = $em->createQueryBuilder();
                    $queryBuilder->add('select', 'st.slotId'
                            )
                            ->add('from', 'Admin\Entity\TimeSlot st')
                            ->where("st.time='" . $totaltime . "'");
                    $data3 = $queryBuilder->getQuery()->getArrayResult();
                    $slotId = $data3[0]['slotId'];
            
            //share cab
            $bsession = new \Zend\Session\Container('Booking' );
            $seatsnos=$bsession->noofseats;
            $uId=$bsession->uid;
           // $seatsnos="1,2";
            $seats=explode(",",$seatsnos);
            //echo count($seats);die;
            if(count($seats)>1)
            {
                for($s=0;$s<count($seats);$s++)
                {
              $implodestring[]=(string)$seats[$s]."-1" ;
                }
            }
            else
            {
                $implodestring[]=$seats[0]."-1";
            }
            
            $implodestringttl=implode(",",$implodestring);
            
            
            //bookingshare
                      
                       
                       
                          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder->add('select', 'bs.seatNo,'
                        . 'bs.travelerName,'
                        . 'bs.farebysheet,'
                        . 'bs.pickLocId,'
                        . 'bs.dropLocId,'
                        . 'bs.status'
                )
                ->add('from', 'Booking\Entity\Bookingshare bs')
                ->innerJoin('\Booking\Entity\Bookingadd', 'b', Join::WITH, 'bs.bookingId=b.bookingId')
                ->innerJoin('\User\Entity\Signup', 'fp', Join::WITH, 'fp.uId = bs.uId')
             //   ->where('bs.bookingId=' . 3, 'bs.uId=' . 11);
                  ->where("bs.bookingId=" . $recentbookid. " AND bs.uId=".$uId. " AND bs.status='".$implodestringttl."'");
        $data = $queryBuilder->getQuery()->getArrayResult();
      //  print_r($data);die;
                          $pLocId =  $data[0]['pickLocId']; 
                          $dLocId =  $data[0]['dropLocId']; 
						 

                    $cityarr = $em->getRepository('Admin\Entity\Location')->find($pLocId);
                    $pLocName = $cityarr->locname;
                    $cityarr = $em->getRepository('Admin\Entity\Location')->find($dLocId);
                    $dLocName = $cityarr->locname;
                       
                       
        $passenger = '<table width="100%" >
            <tr>
        <td width="36%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold; padding-left:10px;"> Passanger Name </span></span></td>
        <td width="35%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;"> Seat Number</span></td>
        <td width="29%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;"> Fare </span></td>
        <td width="29%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;"> Status </span></td>
        </tr>';
        $seatNo = array();
        $travelerName = array();
        $farebysheet = array();
        $status = array();
        foreach ($data as $val) {
            foreach ($val as $key => $vals) {
                switch ($key) {
                    case "seatNo":
                        $seatNo = explode(",", $vals);
                        break;
                    case "travelerName":
                        $travelerName = explode(",", $vals);
                        break;
                    case "farebysheet":
                        $farebysheet = explode(",", $vals);
                        break;

                    case "status":
                        $status = explode(",", $vals);

                        break;
                }
            }

            for ($num = 0; $num < sizeof($seatNo); $num++) {
                    
                $status1 = explode("-", $status[$num]);
                if ($status1[1] == 1) {
                    $statusTemp = "Confirm";
                } else if ($status1[1] == 2) {
                    $statusTemp = "Cancelled.";
                }
                $passenger.= '
      <tr>
        <td height="35"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px;padding-left:10px; "> ' . ucfirst($travelerName[$num]) . ' </span></td>
        <td><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">' . $seatNo[$num] . '</span></td>
        <td style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">' . $farebysheet[$num] . ' </td>
            <td style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">' . $statusTemp . ' </td>
        </tr>';
            }
        }


        $passenger.= '
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
      </table>';
            
            $farebysheetsum=array_sum($farebysheet);
            

                         
                    //Start Cancellation Policy
                      $em = $this->getEntityManager();
            $queryBuilder1 = $em->createQueryBuilder();
            $queryBuilder1->add('select', 'cp')
                    ->add('from', '\User\Entity\canclationpolicy cp')
                    ->where("cp.uId= '0' and cp.usertype= '4' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' ")
                    ->orderBy('cp.hours', ' ASC')
                    ->getQuery();
            $minhour = $queryBuilder1->getQuery()->getArrayResult();
           // print_r($minhour);die;
//print_r($minhour);die;
            if (!empty($minhour)) {
                $hours = $minhour[0]['hours'];
                $percentage = $minhour[0]['percentage'];
            } else {
                $uid = 0;
                $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->add('select', 'cp')
                        ->add('from', '\User\Entity\canclationpolicy cp')
                        ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' ")
                        ->orderBy('cp.hours', 'ASC')
                        ->getQuery();
                $minhour = $queryBuilder1->getQuery()->getArrayResult();
                if (!empty($minhour)) {
                    $hours = $minhour[0]['hours'];
                    $percentage = $minhour[0]['percentage'];
                } else {
                    $hours = 0;
                    $percentage = 0;
                }
            }

//            echo $pickupdateTime;
                  $sec=array();
                  $canceldateTime=array();
//            print_r($minhour) ;die;
            for($i=0;$i<count($minhour);$i++)
            {
              $sec[$i]=$minhour[$i]['hours']*60*60;
           
                $canceldateTime[] = date("d-M-y h:i A", strtotime($pickupdateTime)- $sec[$i]);
           
            }
          //  print_r($pickupdateTime);
          //   print_r($canceldateTime);die;
//            die;
            $queryBuilder2 = $em->createQueryBuilder();
            $queryBuilder2->add('select', 'camt')
                    ->add('from', '\User\Entity\canclationpolicyminamt camt')
                    ->where("camt.uId= '" . $uId . "' and camt.usertype= '" . $usertype . "'  ")
                    ->getQuery();
            $result2 = $queryBuilder2->getQuery()->getArrayResult();

            if (!empty($result2)) {
                $mindeductionamount = $result2[0]['minamt'];
            } else {
                $uId = 0;
                $queryBuilder2 = $em->createQueryBuilder();
                $queryBuilder2->add('select', 'camt')
                        ->add('from', '\User\Entity\canclationpolicyminamt camt')
                        ->where("camt.uId= '0' and camt.usertype= '4'")
                        ->getQuery();
                $result3 = $queryBuilder2->getQuery()->getArrayResult();
                if (!empty($result3)) {
                    $mindeductionamount = $result3[0]['minamt'];
                } else {
                    $mindeductionamount = 0;
                }
            }
            if ($hours < $hourdiff) {
                $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->add('select', 'cp')
                        ->add('from', '\User\Entity\canclationpolicy cp')
                        ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' and cp.hours < '" . $hourdiff . "' ")
                        ->orderBy('cp.hours', ' DESC')
                        ->setMaxResults(1)
                        ->getQuery();
                $result = $queryBuilder1->getQuery()->getArrayResult();
                if (!empty($result)) {
                    $hours = $result[0]['hours'];
                    $percentage = $result[0]['percentage'];
                } else {
                    $uId = 0;
                    $em = $this->getEntityManager();
                    $queryBuilder1 = $em->createQueryBuilder();
                    $queryBuilder1->add('select', 'cp')
                            ->add('from', '\User\Entity\canclationpolicy cp')
                            ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' and cp.hours < '" . $hourdiff . "' ")
                            ->orderBy('cp.hours', ' DESC')
                            ->setMaxResults(1)
                            ->getQuery();
                    $result = $queryBuilder1->getQuery()->getArrayResult();
                    if (!empty($result)) {
                        $hours = $result[0]['hours'];
                        $percentage = $result[0]['percentage'];
                    }
                }
            }
            //End Cancellation Policy
             $data['pickupdateTime'] = $pickupdateTime;
            $data['canceldateTime'] = $canceldateTime;
            $data['defaultmindeduction'] = $mindeductionamount;
            $data['policyhour'] = $minhour;   
           // print_r($data);die;
            
            
$tcpolicystring='<div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
  <div style="width:680px; float:left; height:auto;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold; padding-left:10px; text-decoration:underline;">
        Term & Condition
        </div>
1) Pickup time will be informed 2 hrs. in advance. Passengers are requested to be ready at the given time. Vehicle shall wait only for five minutes at each pickup point.

2) If passengers are not ready at the given pickup time. Passengers will have to come to the last pickup by their own transport. Unless otherwise the vehicle will leave without the passengers in that case ticket charges shall not be refunded. 

3) Count your no. of luggage belonging at the time of loading & unloading. No complaint will be entertained later. 

4) Forgotten baggage or left items will have to be collected from Jaidev Office.

5) Passengers travel in the Cab at their own risk. Company does not accept any liability or responsibility for any loss, damage, accident, delay or break down caused during the journey.

6) Smoking and consumption of liquor in the Cab is Prohibited.

7) Passengers are prohibited from carrying any contraband explosives / inflammable articles. Such passengers shall be asked to leave the Cab immediately.

8) Any delay during the journey beyond our control is not our responsibility A/C charges shall not be refunded if A/C fails during the journey. A/C is complimentary.

9) Tickets issued subject to Aurangabad jurisdiction.  
</div></div> ';                
                    
            
            
                $html="";
              $html .= '<table width="100%" border="1"><tr><td class="rateTableTotalCol"> Cancellation Time ' . '</td><td class="rateTableTotalCol"> ' . ' Charges % ' . '</td><td class="rateTableTotalCol"> ' . 'Minimum  Charges ' . '</td></tr>';
                              //  html += '<tr><td class="rateTableCol">' + 'Between  ' + data['canceldateTime'][0] + " AND " + data['pickupdateTime'] + '</td><td class="rateTableCol">' + 100 + '</td></td> <td class="rateTableCol">' + data['defaultmindeduction'] + '</td> </tr>';
                                $counter = 1;
                                $j = 0;
                                for ($i = 0; $i < count($data['canceldateTime']); $i++)
                                {
//                                    var j=(i)+1;

                                    $j = $counter++;
                                    if ($j >= count($data['canceldateTime']))
                                    {
                                        $j = (count($data['canceldateTime'])) - 1;
                                        $k = $j + 1;
                                    }
                                    if ($k >= count($data['canceldateTime']))
                                    {
//echo $data['canceldateTime'][$i];

//                               alert(j)
                                        $html .= '<tr><td class="rateTableCol">' . 'Before  ' . $data['canceldateTime'][$i] . '</td><td class="rateTableCol">' .$data['policyhour'][$i]['percentage'] . '</td> <td class="rateTableCol">' . $data['defaultmindeduction'] . '</td></tr>';
                                    }
                                    else {
                                        $html .= '<tr><td class="rateTableCol">' . 'Between  ' . $data['canceldateTime'][$j] . " AND  " . $data['canceldateTime'][$i] . '</td><td class="rateTableCol">' . $data['policyhour'][$i]['percentage'] . '</td> <td class="rateTableCol">' . $data['defaultmindeduction'] . '</td></tr>';
                                    }
                                }
            
           // echo $html;die;
                    
  $resultMail = $client->sendEmail(array(
                        // Source is required
                        'Source' => '"'.$userCompanyName.'" <noreply@clearcarrental.com>',
                        // Destination is required
                        'Destination' => array(
                            'ToAddresses' => array($emailidVal), 
                            'BccAddresses' => array('shriram.chaudhari@infogird.com','jaidevcoolcab@gmail.com'),
                        ),
                        // Message is required
                        'Message' => array(
                            // Subject is required
                            'Subject' => array(
                                // Data is required
                                'Data' => $userCompanyName." Booking Details ".$genrefno,
                                'Charset' => 'UTF-8',
                            ),
                            // Body is required
                            'Body' => array(
                                'Text' => array(
                                    // Data is required
                                    'Data' => 'Hello Guys how all you feel when using ZF2',
                                    'Charset' => 'UTF-8',
                                ),
                                'Html' => array(
                                    // Data is required
                                    'Data' => '<title>Registration</title>

<html>

<body>
<div style="width:700px; height:auto; float:left; font-family:Calibri, Arial; background:#eaeaea; padding:10px;">

<div style="width:698px; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
       <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:2px solid #CCCCCC; padding-bottom:5px;">

<div style="width:200px; height:50px; float:left; display:inline-block; background:#fff; 
            margin-left		:0px; clip-path: circle(60px at center); vertical-align:middle; text-align:left; 
            padding:10px 10px 10px 10px; "> 
            <img src="'.$loginurllogo.'"  /> 
        </div>
        <div style="float:left; height:auto; width:170px; margin-left:275px; margin-top:20px;">
              
        <div style="height:auto; width:100%;">
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; color: #666666""><strong>Customer Care</strong></div>
          
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; color: #666666">
          9822029700, 0240-23337333, 0240-23338333</div>
        </div>
       </div>
    </div>
   <div style="width:698px; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">

	<div style="width:690px; height:100%; float:left;  background:#fff;">  
        <div style=" font-family:Calibri, Arial;font-size:20px; color:#666666; text-align:left; float:left; 
        width:300px; line-height:50px; font-weight:bold; padding-left:10px; ">'.$pCityName.'  --> '.$dCityName.'</div>
             
        <div style=" font-family:Calibri, Arial; font-size:18px; color:#666666; text-align:left; float:left; 
          line-height:50px; font-weight:bold; ">'.$tDate.'</div>
         
        <div style="float:left; height:auto; width:170px; margin-left:93px;">
              
        <div style="height:auto; width:100%;">
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; line-height:25px; color: #666666">CRN No.</div>
          
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; line-height:25px; color: #666666">'.$genrefno.'</div>
        </div>
       </div>
			</div>
   </div>
       
              <!-- passanger EmailID-->

       
       
       <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
	
    <div style="width:460px; float:left; height:auto;  padding-left:10px" >
    
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold;">Email ID </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; ">'.$travellerEmailId.'</div>
  
    </div>
	<div style="width:200px; float:left; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;">Conatact Number
        </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">
        '.$travellerContactNo.'
        </div>
    </div>

</div>

  <!-- passanger Information-->

'.$passenger.'
       <!-- passanger Information--><!-- Bus Type Information-->


 <!-- Boarding Information-->
 <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
 
 <div style="width:680px; padding-left:10px; height:50px; line-height:50px; color:#333; font-size:20px; font-weight:bold; font-family:Calibri, Arial;font-family:Calibri, Arial;">
	Boarding point details
</div>
	<div style="width:220px; float:left; height:auto; padding-left:10px">
            <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">
        Car / Bus Type
        </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">'.$vehicleName.'</div>

    </div>
    <div style="width:220px; float:left; height:auto; margin-left:20px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Pickup TIme</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">
         '.$pickupTime.'
        </div>
    </div>
	<div style="width:200px; float:left; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Pickup</span> Location</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">'.$pLocName.'</div>
    </div>
</div>
 <!-- Operator Information-->  


<div style="width:100%; height:auto; float:left; background:#fff; padding-bottom:5px;"></div>

 
 <!-- Total Charges-->

<div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
  <div style="width:100%; height:auto; float:left; background:#fff; padding-bottom:5px;">
	<div style="width:220px; float:left; height:auto; padding-left:10px">
    
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Total Fare</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">Rs '.$farebysheetsum.'</div>

    </div>
    
    
    
	<div style="width:200px; float:right; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Drop</span> Location</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">'.$dLocName.'</div>
    </div>

</div>

</div>


 <!-- Term & Condition-->
'.nl2br($tcpolicystring).'

 

<div style="height:auto; width:100%; float:left; margin-top:10px;">


           <div style="height:auto; width:100%;  font-family:Calibri, Arial;font-size:14px; padding-left:10px; 
           color:#666666; text-align:left; line-height:20px; font-weight:bold"><!--
			Cancellation Policy
           --></div>

            
          '.$html.'
                      
    </div>

</div>
</div>
</body>
</html>',
                                    // 'Data' => 'Your Login Url is "'.$loginurl.'"',
                                    'Charset' => 'UTF-8',
                                ),
                            ),
                        ),
                            //    'ReplyToAddresses' => array('noreply@clearcarrental.com'),
                            //    'ReturnPath' => 'suppport@clearcarrental.com',
                    )); 
  return true;
         exit;
     }
     
 public function bookingmailerdeal() {
         
         $hosturlCurrnet=$_SERVER['HTTP_HOST'];
        $finalurl=$this->getRealUrl();
         $aws = $this->getServiceLocator()->get('aws');
        $client = $aws->get('ses');
         
           
 $hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        if($companyentity->logo!="")
        {
            $userCompanyLogo=$companyentity->logo;
        }
        else
        {
            $userCompanyLogo="logo.jpg";
        }
        $logopath='/images/company/clogo/'.$userCompanyLogo;
         if($loginenv=="cabsaas")
                                                        {
         $loginurl = "https://" . $hosturlCurrnet . "/signin";
        $loginurllogo = "https://" . $loginurl1.$logopath;
                                                        }
                                                        else
                                                        {
                                                          $loginurl = "http://" . $hosturlCurrnet . "/signin";
        $loginurllogo = "http://" . $loginurl1.$logopath;  
                                                        }
       // $companynameVal=$userCompanyName;
                    
               //die;     
                    
          
                       $bsession = new \Zend\Session\Container('Booking' );
        $recentbookid = $bsession->recentbookid;
        $recentuidvalue=$bsession->uid;
       //  $recentbookid=7;
         $em = $this->getEntityManager();
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bookadd')
                        ->add('from', '\Booking\Entity\Bookingadd bookadd')
                       ->where("bookadd.bookingId='".$recentbookid."'")
                        ->getQuery();
                $resultBking = $queryBuilder->getQuery()->getArrayResult();
                //print_r($resultBking);die;
                
                   $recentbookid =  $resultBking[0]['bookingId'];
                    $genrefno =  $resultBking[0]['refNo'];
                     $tDate =  $resultBking[0]['tDate'];
                    $vehicleid = $resultBking[0]['vehicleId'];
                     $pCity =  $resultBking[0]['pCity'];
                     $dCity =  $resultBking[0]['dCity'];
                     $pickupTime =  $resultBking[0]['pickupTime'];
                     $typeofservice = $resultBking[0]['travelType'];
                     $totalBasicFare = $resultBking[0]['totalBasicFare'];
                      $pickupdateTime = date("d-M-y h:i A", strtotime($resultBking[0]['tDate']." ".$resultBking[0]['pickupTime']));
                      
                      $cityarr = $em->getRepository('Admin\Entity\City')->find($pCity);
                    $pCityName = $cityarr->ctname;
                      $cityarr = $em->getRepository('Admin\Entity\City')->find($dCity);
                    $dCityName = $cityarr->ctname;
                    $vehicleyarr = $em->getRepository('Admin\Entity\Vehicle')->find($vehicleid);
                    $vehicleName = $vehicleyarr->vehicleName;
                // die; 
                    //Booking Traveller
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->add('select', 'bt')
                        ->add('from', '\Booking\Entity\Bookingtraveller bt')
                        ->where("bt.bookingId='".$recentbookid."' AND bt.uId=".$recentuidvalue)
                        ->getQuery();
                $resultTrler = $queryBuilder->getQuery()->getArrayResult();
                    
                 $travellerName =  $resultTrler[0]['travellerName'];
                    $travellerEmailId =  $resultTrler[0]['travellerEmailId'];   
                     $travellerContactNo =  $resultTrler[0]['travellerContactNo'];   
                    
                     // $uId =  $resultTrler[0]['uId']; 
                    //   $location =  $resultTrler[0]['location']; 
                       
//                        $userrarr = $em->getRepository('User\Entity\Signup')->find($recentuidvalue);
//                    $travellerEmailId = $userrarr->email;
                      
                       
                       
                    $emailidVal = $travellerEmailId;   
                    
                    
                    
                    //bookingshare
                      
                       
                       
                          $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder->add('select', 'bs.seatNo,'
                        . 'bs.travelerName,'
                        . 'bs.farebysheet,'
                        . 'bs.pickLocId,'
                        . 'bs.dropLocId,'
                        . 'bs.status'
                )
                ->add('from', 'Booking\Entity\Bookingshare bs')
                ->innerJoin('\Booking\Entity\Bookingadd', 'b', Join::WITH, 'bs.bookingId=b.bookingId')
                ->innerJoin('\User\Entity\Signup', 'fp', Join::WITH, 'fp.uId = bs.uId')
             //   ->where('bs.bookingId=' . 3, 'bs.uId=' . 11);
                  ->where("bs.bookingId=" . $recentbookid. " AND bs.uId=".$recentuidvalue. " AND b.status=1");
        $data = $queryBuilder->getQuery()->getArrayResult();
      //  print_r($data);die;
                          $pLocId =  $data[0]['pickLocId']; 
                          $dLocId =  $data[0]['dropLocId']; 
						 

                    $cityarr = $em->getRepository('Admin\Entity\Location')->find($pLocId);
                    $pLocName = $cityarr->locname;
                    $cityarr = $em->getRepository('Admin\Entity\Location')->find($dLocId);
                    $dLocName = $cityarr->locname;
                    
                       
//                    $dealsarr = $em->getRepository('Tariff\Entity\Deals')->find($bsession->dealId);
//                      $dealploc = $dealsarr->pickLocId; 
//                       $dealdloc = $dealsarr->dropLocId; 
//                    
//                           $cityarr = $em->getRepository('Admin\Entity\Location')->find($pLocId);
//                    $pLocName = $cityarr->locname;
//                      $cityarr = $em->getRepository('Admin\Entity\Location')->find($dLocId);
//                    $dLocName = $cityarr->locname;          
//        
//            
//              //TimeSlotId
//          
//            
//             $splitime=explode(" ",$pickupTime);
//                    $splitime2=explode(":",$splitime[0]);
//                    $ampm=$splitime[1];
//                    if($ampm=="am")
//                    {
//                        $hours=$splitime2[0];
//                        $minuts=$splitime2[1];
//                        $totaltime=$hours.":".$minuts.":00";
//                    }
//                    else
//                    {
//                        $hours=$splitime2[0];
//                        $hours2=$hours+12;
//                        $minuts=$splitime2[1];
//                        $totaltime=$hours.":".$minuts.":00";
//                    }
//                 //   echo $totaltime;
//                    
//                    $queryBuilder = $em->createQueryBuilder();
//                    $queryBuilder->add('select', 'st.slotId'
//                            )
//                            ->add('from', 'Admin\Entity\TimeSlot st')
//                            ->where("st.time='" . $totaltime . "'");
//                    $data3 = $queryBuilder->getQuery()->getArrayResult();
//                    $slotId = $data3[0]['slotId'];
//            
//            //share cab
//            $bsession = new \Zend\Session\Container('Booking' );
//            $seatsnos=$bsession->noofseats;
//            $uId=$bsession->uid;
//           // $seatsnos="1,2";
//            $seats=explode(",",$seatsnos);
//            //echo count($seats);die;
//            if(count($seats)>1)
//            {
//                for($s=0;$s<count($seats);$s++)
//                {
//              $implodestring[]=(string)$seats[$s]."-1" ;
//                }
//            }
//            else
//            {
//                $implodestring[]=$seats[0]."-1";
//            }
//            
//            $implodestringttl=implode(",",$implodestring);
//            
//            
//            //bookingshare
//                      
//                       
//                       
//                          $em = $this->getEntityManager();
//        $queryBuilder = $em->createQueryBuilder();
//
//        $queryBuilder->add('select', 'bs.seatNo,'
//                        . 'bs.travelerName,'
//                        . 'bs.farebysheet,'
//                        . 'bs.pickLocId,'
//                        . 'bs.dropLocId,'
//                        . 'bs.status'
//                )
//                ->add('from', 'Booking\Entity\Bookingshare bs')
//                ->innerJoin('\Booking\Entity\Bookingadd', 'b', Join::WITH, 'bs.bookingId=b.bookingId')
//                ->innerJoin('\User\Entity\Signup', 'fp', Join::WITH, 'fp.uId = bs.uId')
//             //   ->where('bs.bookingId=' . 3, 'bs.uId=' . 11);
//                  ->where("bs.bookingId=" . $recentbookid. " AND bs.uId=".$uId. " AND bs.status='".$implodestringttl."'");
//        $data = $queryBuilder->getQuery()->getArrayResult();
//      //  print_r($data);die;
//          $pLocId =  $data[0]['pickLocId']; 
//                       $dLocId =  $data[0]['dropLocId']; 

                       
                       
        $passenger = '<table width="100%" >
            <tr>
        <td width="36%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold; padding-left:10px;"> Passanger Name </span></span></td>
        <td width="29%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;"> Fare </span></td>
        <td width="29%"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;"> Status </span></td>
        </tr>
      <tr>
        <td height="35"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px;padding-left:10px; "> ' . ucfirst($travellerName) . ' </span></td>
        <td style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">' . $totalBasicFare . ' </td>
            <td style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">Confirm </td>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
      </table>';
            
          //  $farebysheetsum=array_sum($farebysheet);
            

                         
                    //Start Cancellation Policy
                      $em = $this->getEntityManager();
            $queryBuilder1 = $em->createQueryBuilder();
            $queryBuilder1->add('select', 'cp')
                    ->add('from', '\User\Entity\canclationpolicy cp')
                    ->where("cp.uId= '0' and cp.usertype= '4' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' ")
                    ->orderBy('cp.hours', ' ASC')
                    ->getQuery();
            $minhour = $queryBuilder1->getQuery()->getArrayResult();
           // print_r($minhour);die;
//print_r($minhour);die;
            if (!empty($minhour)) {
                $hours = $minhour[0]['hours'];
                $percentage = $minhour[0]['percentage'];
            } else {
                $uid = 0;
                $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->add('select', 'cp')
                        ->add('from', '\User\Entity\canclationpolicy cp')
                        ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' ")
                        ->orderBy('cp.hours', 'ASC')
                        ->getQuery();
                $minhour = $queryBuilder1->getQuery()->getArrayResult();
                if (!empty($minhour)) {
                    $hours = $minhour[0]['hours'];
                    $percentage = $minhour[0]['percentage'];
                } else {
                    $hours = 0;
                    $percentage = 0;
                }
            }

//            echo $pickupdateTime;
                  $sec=array();
                  $canceldateTime=array();
//            print_r($minhour) ;die;
            for($i=0;$i<count($minhour);$i++)
            {
              $sec[$i]=$minhour[$i]['hours']*60*60;
           
                $canceldateTime[] = date("d-M-y h:i A", strtotime($pickupdateTime)- $sec[$i]);
           
            }
          //  print_r($pickupdateTime);
          //   print_r($canceldateTime);die;
//            die;
            $queryBuilder2 = $em->createQueryBuilder();
            $queryBuilder2->add('select', 'camt')
                    ->add('from', '\User\Entity\canclationpolicyminamt camt')
                    ->where("camt.uId= '" . $uId . "' and camt.usertype= '" . $usertype . "'  ")
                    ->getQuery();
            $result2 = $queryBuilder2->getQuery()->getArrayResult();

            if (!empty($result2)) {
                $mindeductionamount = $result2[0]['minamt'];
            } else {
                $uId = 0;
                $queryBuilder2 = $em->createQueryBuilder();
                $queryBuilder2->add('select', 'camt')
                        ->add('from', '\User\Entity\canclationpolicyminamt camt')
                        ->where("camt.uId= '0' and camt.usertype= '4'")
                        ->getQuery();
                $result3 = $queryBuilder2->getQuery()->getArrayResult();
                if (!empty($result3)) {
                    $mindeductionamount = $result3[0]['minamt'];
                } else {
                    $mindeductionamount = 0;
                }
            }
            if ($hours < $hourdiff) {
                $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->add('select', 'cp')
                        ->add('from', '\User\Entity\canclationpolicy cp')
                        ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' and cp.hours < '" . $hourdiff . "' ")
                        ->orderBy('cp.hours', ' DESC')
                        ->setMaxResults(1)
                        ->getQuery();
                $result = $queryBuilder1->getQuery()->getArrayResult();
                if (!empty($result)) {
                    $hours = $result[0]['hours'];
                    $percentage = $result[0]['percentage'];
                } else {
                    $uId = 0;
                    $em = $this->getEntityManager();
                    $queryBuilder1 = $em->createQueryBuilder();
                    $queryBuilder1->add('select', 'cp')
                            ->add('from', '\User\Entity\canclationpolicy cp')
                            ->where("cp.uId= '" . $uId . "' and cp.usertype= '" . $usertype . "' and cp.typeofservice LIKE '" . '%' . $typeofservice . '%' . "' and cp.hours < '" . $hourdiff . "' ")
                            ->orderBy('cp.hours', ' DESC')
                            ->setMaxResults(1)
                            ->getQuery();
                    $result = $queryBuilder1->getQuery()->getArrayResult();
                    if (!empty($result)) {
                        $hours = $result[0]['hours'];
                        $percentage = $result[0]['percentage'];
                    }
                }
            }
            //End Cancellation Policy
             $data['pickupdateTime'] = $pickupdateTime;
            $data['canceldateTime'] = $canceldateTime;
            $data['defaultmindeduction'] = $mindeductionamount;
            $data['policyhour'] = $minhour;   
           // print_r($data);die;
            
            
$tcpolicystring='<div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
  <div style="width:680px; float:left; height:auto;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold; padding-left:10px; text-decoration:underline;">
        Term & Condition
        </div>
1) Pickup time will be informed 2 hrs. in advance. Passengers are requested to be ready at the given time. Vehicle shall wait only for five minutes at each pickup point.

2) If passengers are not ready at the given pickup time. Passengers will have to come to the last pickup by their own transport. Unless otherwise the vehicle will leave without the passengers in that case ticket charges shall not be refunded. 

3) Count your no. of luggage belonging at the time of loading & unloading. No complaint will be entertained later. 

4) Forgotten baggage or left items will have to be collected from Jaidev Office.

5) Passengers travel in the Cab at their own risk. Company does not accept any liability or responsibility for any loss, damage, accident, delay or break down caused during the journey.

6) Smoking and consumption of liquor in the Cab is Prohibited.

7) Passengers are prohibited from carrying any contraband explosives / inflammable articles. Such passengers shall be asked to leave the Cab immediately.

8) Any delay during the journey beyond our control is not our responsibility A/C charges shall not be refunded if A/C fails during the journey. A/C is complimentary.

9) Tickets issued subject to Aurangabad jurisdiction.  
</div></div> ';                
                    
            
            
                $html="";
              $html .= '<table width="100%" border="1"><tr><td class="rateTableTotalCol"> Cancellation Time ' . '</td><td class="rateTableTotalCol"> ' . ' Charges % ' . '</td><td class="rateTableTotalCol"> ' . 'Minimum  Charges ' . '</td></tr>';
                              //  html += '<tr><td class="rateTableCol">' + 'Between  ' + data['canceldateTime'][0] + " AND " + data['pickupdateTime'] + '</td><td class="rateTableCol">' + 100 + '</td></td> <td class="rateTableCol">' + data['defaultmindeduction'] + '</td> </tr>';
                                $counter = 1;
                                $j = 0;
                                for ($i = 0; $i < count($data['canceldateTime']); $i++)
                                {
//                                    var j=(i)+1;

                                    $j = $counter++;
                                    if ($j >= count($data['canceldateTime']))
                                    {
                                        $j = (count($data['canceldateTime'])) - 1;
                                        $k = $j + 1;
                                    }
                                    if ($k >= count($data['canceldateTime']))
                                    {
//echo $data['canceldateTime'][$i];

//                               alert(j)
                                        $html .= '<tr><td class="rateTableCol">' . 'Before  ' . $data['canceldateTime'][$i] . '</td><td class="rateTableCol">' .$data['policyhour'][$i]['percentage'] . '</td> <td class="rateTableCol">' . $data['defaultmindeduction'] . '</td></tr>';
                                    }
                                    else {
                                        $html .= '<tr><td class="rateTableCol">' . 'Between  ' . $data['canceldateTime'][$j] . " AND  " . $data['canceldateTime'][$i] . '</td><td class="rateTableCol">' . $data['policyhour'][$i]['percentage'] . '</td> <td class="rateTableCol">' . $data['defaultmindeduction'] . '</td></tr>';
                                    }
                                }
            
           // echo $html;die;
                    
  $resultMail = $client->sendEmail(array(
                        // Source is required
                        'Source' => '"'.$userCompanyName.'" <noreply@clearcarrental.com>',
                        // Destination is required
                        'Destination' => array(
                            'ToAddresses' => array($emailidVal),
							'BccAddresses' => array('shriram.chaudhari@infogird.com','jaidevcoolcab@gmail.com'),
                        ),
                        // Message is required
                        'Message' => array(
                            // Subject is required
                            'Subject' => array(
                                // Data is required
                                'Data' => $userCompanyName." Booking Details ".$genrefno,
                                'Charset' => 'UTF-8',
                            ),
                            // Body is required
                            'Body' => array(
                                'Text' => array(
                                    // Data is required
                                    'Data' => 'Hello Guys how all you feel when using ZF2',
                                    'Charset' => 'UTF-8',
                                ),
                                'Html' => array(
                                    // Data is required
                                    'Data' => '<title>Registration</title>

<html>

<body>
<div style="width:700px; height:auto; float:left; font-family:Calibri, Arial; background:#eaeaea; padding:10px;">

<div style="width:698px; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
       <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:2px solid #CCCCCC; padding-bottom:5px;">

<div style="width:200px; height:50px; float:left; display:inline-block; background:#fff; 
            margin-left		:0px; clip-path: circle(60px at center); vertical-align:middle; text-align:left; 
            padding:10px 10px 10px 10px; "> 
            <img src="'.$loginurllogo.'"  /> 
        </div>
        <div style="float:left; height:auto; width:170px; margin-left:275px; margin-top:20px;">
              
        <div style="height:auto; width:100%;">

          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; color: #666666""><strong>Customer Care</strong></div>
          
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; color: #666666">
          9822029700, 0240-23337333, 0240-23338333</div>
        </div>
       </div>
    </div>
   <div style="width:698px; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">

	<div style="width:690px; height:100%; float:left;  background:#fff;">  
        <div style=" font-family:Calibri, Arial;font-size:20px; color:#666666; text-align:left; float:left; 
        width:300px; line-height:50px; font-weight:bold; padding-left:10px; ">'.$pCityName.'  --> '.$dCityName.'</div>
             
        <div style=" font-family:Calibri, Arial; font-size:18px; color:#666666; text-align:left; float:left; 
          line-height:50px; font-weight:bold; ">'.$tDate.'</div>
         
        <div style="float:left; height:auto; width:170px; margin-left:93px;">
              
        <div style="height:auto; width:100%;">
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; line-height:25px; color: #666666">CRN No.</div>
          
          <div style="width:170px; height:100%; float:left;  background:#fff; 
          font-family: Calibri, Arial; font-size: 15px; line-height:25px; color: #666666">'.$genrefno.'</div>
        </div>
       </div>
			</div>
   </div>
       
              <!-- passanger EmailID-->

       
       
       <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
	
    <div style="width:460px; float:left; height:auto;  padding-left:10px" >
    
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; font-weight:bold;">Email ID </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:100%; 
        line-height:30px; ">'.$travellerEmailId.'</div>
  
    </div>
	<div style="width:200px; float:left; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;">Conatact Number
        </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">
        '.$travellerContactNo.'
        </div>
    </div>

</div>

  <!-- passanger Information-->

'.$passenger.'
       <!-- passanger Information--><!-- Bus Type Information-->


 <!-- Boarding Information-->
 <div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
 
 <div style="width:680px; padding-left:10px; height:50px; line-height:50px; color:#333; font-size:20px; font-weight:bold; font-family:Calibri, Arial;font-family:Calibri, Arial;">
	Boarding point details
</div>
	<div style="width:220px; float:left; height:auto; padding-left:10px">
    
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">
        Car / Bus Type
        </div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">'.$vehicleName.'</div>

    </div>
    
    
    
    <div style="width:220px; float:left; height:auto; margin-left:20px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Pickup TIme</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">
         '.$pickupTime.'
        </div>
    </div>



	<div style="width:200px; float:left; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Pickup</span> Location</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">'.$pLocName.'</div>
    </div>

</div>
 
 
 
 
 





 <!-- Operator Information-->


<div style="width:100%; height:auto; float:left; background:#fff; padding-bottom:5px;"></div>


 <!-- Total Charges-->

<div style="width:100%; height:auto; float:left; background:#fff; border-bottom:1px solid #cccccc; padding-bottom:5px;">
  <div style="width:100%; height:auto; float:left; background:#fff; padding-bottom:5px;">
	<div style="width:220px; float:left; height:auto; padding-left:10px">
    
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Total Fare</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; ">Rs '.$totalBasicFare.'</div>

    </div>
    
    
    
	<div style="width:200px; float:right; height:auto; margin-left:24px;">
    	 <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; font-weight:bold;"><span style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:220px; 
        line-height:30px; font-weight:bold;">Drop</span> Location</div>
        <div style="font-family:Calibri, Arial;font-size:15px; color:#666666; text-align:left; width:200px; 
        line-height:30px; ">'.$dLocName.'</div>
    </div>

</div>

</div>


 <!-- Term & Condition-->
'.nl2br($tcpolicystring).'

 

<div style="height:auto; width:100%; float:left; margin-top:10px;">


           <div style="height:auto; width:100%;  font-family:Calibri, Arial;font-size:14px; padding-left:10px; 
           color:#666666; text-align:left; line-height:20px; font-weight:bold"><!--
			Cancellation Policy
           --></div>

            
          '.$html.'
                      
    </div>

</div>
</div>
</body>
</html>',
                                    // 'Data' => 'Your Login Url is "'.$loginurl.'"',
                                    'Charset' => 'UTF-8',
                                ),
                            ),
                        ),
                            //    'ReplyToAddresses' => array('noreply@clearcarrental.com'),
                            //    'ReturnPath' => 'suppport@clearcarrental.com',
                    )); 
  return true;
         exit;
     }    
	
public function smssender()
{
    
     $hosturlCurrnet=$_SERVER['HTTP_HOST'];
        $finalurl=$this->getRealUrl();
       
 $hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
    
    
    $psession = new \Zend\Session\Container('Payment');
    $customerName=$psession->fname;
    $customerMobile=$psession->contactno;
    $genrefno=$psession->genrefno;
 
	 $bsession = new \Zend\Session\Container('Booking' );
	 $customerName=$bsession->fulname;
	 $customerMobile=$bsession->mobile;
	 $genrefno=$bsession->genrefno;
	
    
     $bsession = new \Zend\Session\Container('Booking');
     $bookstatuschange= $this->getEntityManager()->getRepository('Booking\Entity\Bookingadd')->findOneBy(array('bookingId'=>$bsession->recentbookid) ); 
     
     $travelRootvalue=$bookstatuschange->travelRoot;
     $pickupTimevalue=$bookstatuschange->pickupTime;
     $tDatevalue=$bookstatuschange->tDate;
     $vehicleIdvalue=$bookstatuschange->vehicleId;
     
     $pCityvalue=$bookstatuschange->pCity;
     
      $refNo=$bookstatuschange->refNo;
     
     $dCityvalue=$bookstatuschange->dCity;
    
      $TarriffSubListarr = $this->getEntityManager()->find('Admin\Entity\TarriffSubList', $travelRootvalue);
      $TarriffSubListName=$TarriffSubListarr->tariffSubName;
      
       $vehicleListarr = $this->getEntityManager()->find('Admin\Entity\Vehicle', $vehicleIdvalue);
      $vehicleName=$vehicleListarr->vehicleName;
	  
	  
	    
      
       $pcityarr = $this->getEntityManager()->find('Admin\Entity\City', $pCityvalue);
      $pcityName=$pcityarr->ctname;
    $dcityarr = $this->getEntityManager()->find('Admin\Entity\City', $dCityvalue);
      $dcityName=$dcityarr->ctname;
	   
	  
    //Your authentication key
    $authKey = "96570A1CzpiWx1Q5633079f";

$seatsarr=$bsession->noofseats;
$seats=$seatsarr;
//Multiple mobiles numbers separated by comma
$mobileNumber = "91".$customerMobile.",919850302040,918888801810,919421688535";

//Sender ID,While using route4 sender id should be 6 characters long.
$senderId = "JAIDEV";

//Your message to send, Add URL encoding here.
if($travelRootvalue!="12")
{
$bookingMsg="Dear ".$customerName.", thank you for choosing ".$userCompanyName." for ".$TarriffSubListName." ".$pcityName." to ".$dcityName." ".$vehicleName." for seat no. ".$seats."  Dated ".$tDatevalue." at ".$pickupTimevalue.". Your ref no is ".$refNo.". More details check you email or website www.jaidevcoolcab.com T&C apply.";
}
else
{
    $bookingMsg="Dear ".$customerName.", thank you for choosing ".$userCompanyName." for ".$TarriffSubListName." ".$pcityName." to ".$dcityName." ".$vehicleName."  Dated ".$tDatevalue." at ".$pickupTimevalue.". Your ref no is ".$refNo.". More details check you email or website www.jaidevcoolcab.com T&C apply.";
}
//echo $bookingMsg;
$message = urlencode($bookingMsg);

//Define route 
$route = "4";
//Prepare you post parameters
$postData = array(
    'authkey' => $authKey,
    'mobiles' => $mobileNumber,
    'message' => $message,
    'sender' => $senderId,
    'route' => $route,
    'country' => "91"
);

//API URL
$url="https://control.msg91.com/sendhttp.php";

// init the resource
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
    //,CURLOPT_FOLLOWLOCATION => true
));


//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


//get response
$output = curl_exec($ch);

//Print error if any
if(curl_errno($ch))
{
    echo 'error:' . curl_error($ch);
}

curl_close($ch);
return true;
exit;
echo $output;
}
    
public function getenv()
{
    $testing=$this->Uri_type; 
    return $testing;
}


public function getwalletAction()
{
	$email = filter_input(INPUT_POST, 'email');
            $em = $this->getEntityManager();
                $queryBuilder1 = $em->createQueryBuilder();
               $queryBuilder1->add('select', 'sup')
                        ->add('from', '\User\Entity\Signup sup')
                        ->where("(sup.email)= '" .  $email . "'  ")
                        ->getQuery();
                $result = $queryBuilder1->getQuery()->getArrayResult();
              $resuid= $result[0]['uId'];		  
			  
			  if ( $resuid>0) {
				  
                $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->add('select', 'fc.balance')
                    ->add('from', '\Finance\Entity\Fcreditdebit fc')
                    ->where("fc.uId =" . $resuid)
					 ->orderBy('fc.cdId', 'DESC')
                        ->getQuery() ;
        
        $results = $queryBuilder->getQuery()->getArrayResult();
		$resbalance= $results[0]['balance'];
        if (count($results) > 0) {
           // echo json_encode($results);
			echo '{"balance":'.$resbalance.'}';
        } else {
            echo '{"balance":0}';
			
        }
		}
		else
		{
			echo '{"balance":0}';
			}
			  
        // echo json_encode($resuid);
		 exit;
	
	}

		public function forwardtovendor($data) { 
        $Bookingvendor = new Bookingvendor(); 
        $Bookingvendor->exchangeArray($data[0]);   
        if ($data[0]['approxDistance'] > 0) { 
            $this->getEntityManager()->persist($Bookingvendor);
            $this->getEntityManager()->flush(); 
            $Bookingadd = $this->getEntityManager()->find('Booking\Entity\Bookingadd', $data[0]['id']);
            $Bookingadd->fuId = $data[0]['uId'];
            $this->getEntityManager()->flush();
         }  
       
	    $fp=$data[0]['companyOwner'];
        $customerMobile=$data[0]['ownerContactNo'];
        $this->smscheckAction($fp,$customerMobile,$data[0]['id']);
       
    }
	
    public function smscheckAction($fp,$customerMobile,$bookingId)
      {
        //call sms sender class
//         $fp=$data['companyOwner'];
//        $customerMobile=$data['ownerContactNo'];
        
         $hosturlCurrnet=$_SERVER['HTTP_HOST'];
         $finalurl=$this->getRealUrl();
       
 		$hosturl=$finalurl;
        $em2=$this->getEntityManager2();
        $loginUrl=$em2->getRepository('Registration\Entity\Registration')->findOneByloginUrl($hosturl);
        $sUid = $loginUrl->sUid;
        $loginurl1 = $loginUrl->loginUrl;
        $em=$this->getEntityManager();
        $userentity=$em->getRepository('User\Entity\Signup')->findOneBysNuId($sUid);
        $uId = $userentity->uId;
        
        $companyentity=$em->getRepository('User\Entity\Company')->findOneByuserid($uId);
        $userCompanyName=$companyentity->companyName;
        $Bookingadd = $this->getEntityManager()->find('Booking\Entity\Bookingadd', $bookingId);
         // $fp="Rohit";
       // $customerMobile="9503131444";
         $travelRootvalue=$Bookingadd->travelRoot;
        $uIdvalue=$Bookingadd->uId;
    $pickupTimevalue=$Bookingadd->pickupTime;
    $tDatevalue=$Bookingadd->tDate;
    $vehicleIdvalue=$Bookingadd->vehicleId;
     
    $pCityvalue=$Bookingadd->pCity;
     
    $refNo=$Bookingadd->refNo;
     
    $dCityvalue=$Bookingadd->dCity;
    
    $TarriffSubListarr = $this->getEntityManager()->find('Admin\Entity\TarriffSubList', $travelRootvalue);
    $TarriffSubListName=$TarriffSubListarr->tariffSubName;
      
    $vehicleListarr = $this->getEntityManager()->find('Admin\Entity\Vehicle', $vehicleIdvalue);
    $vehicleName=$vehicleListarr->vehicleName;
      
    $pcityarr = $this->getEntityManager()->find('Admin\Entity\City', $pCityvalue);
    $pcityName=$pcityarr->ctname;
    $dcityarr = $this->getEntityManager()->find('Admin\Entity\City', $dCityvalue);
    $dcityName=$dcityarr->ctname;
    
//      $customerdata = $this->getEntityManager()->find('Booking\Entity\Signup', $uId);
//    $cFirstName=$customerdata->firstName;
//    $cLastName=$customerdata->lastName;
    
    $bookingforwardMsg="Dear Mr ".$fp.", ".$userCompanyName." New Booking:".$refNo." ".$TarriffSubListName." ".$pcityName." to ".$dcityName." ".$vehicleName." Dated ".$tDatevalue." at ".$pickupTimevalue;
   //echo $bookingforwardMsg;
    $senderidvalue="JAIDEV";
    $smssenderobj = new Smssender();
    $smssenderobj->smssenderfun($customerMobile, $senderidvalue, $bookingforwardMsg);
    return true; 
    }
    
	public function calVendor($root) {
        
        $totalFare = "";
        $totalFareVendor = "";

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
      //  $root = $this->getRequest()->getPost()->textgetviewValueData;
        $myArr = Array();
        $customArray = Array();

        $id = $root;
        $customArray["bVendorId"] = 0;
        $customArray["bookingId"] = 0;
        $customArray["uId"] = 0;
        $customArray["kmRate"] = 0;
        $customArray["kmRateOw"] = 0;
        $customArray["fullDay"] = 0;
        $customArray["halfDay"] = 0;
        $customArray["transfer"] = 0;
        $customArray["approxDistance"] = 0;
        $customArray["minDistancePerDay"] = 0;
        $customArray["minHrPerDay"] = 0;
        $customArray["nightHalt"] = 0;
        $customArray["extraKmRate"] = 0;
        $customArray["extraKmRateOw"] = 0;
        $customArray["extraHrRate"] = 0;
        $customArray["waitingCharges"] = 0;
        $customArray["hillCharges"] = 0;
        $customArray["driverAllowance"] = 0;
        $customArray["tollCharges"] = 0;
        $customArray["parkingCharges"] = 0;
        $customArray["stateCharges"] = 0;
        $customArray["otherCharges"] = 0;
        $customArray["waitingTimeDuration"] = 0;
        $customArray["status"] = 0;



        $queryBuilder->add('select', 'b.bookingId ,'
                        . 'b.status,'
                        . 'bt.uId ,'
                        . 'b.subuserType,'
                        . 'b.refNo as ref_No,'
                        . 'b.noOfDays as No_Of_Days,'
                        . 'b.pCity as Pickup_City,'
                        . 'b.vehicleId as Vehicle_Id,'
                        . 'b.noOfCars as No_Of_Cars,'
                        . 'b.travelRoot as Travel_Root,'
                        . 'b.kmRate as KM_Rate,'
                        . 'b.kmRateOw as KM_Rate_One_way,'
                        . 'b.hrRate as HR_Rate,'
                        . 'b.approxDistance as Approx_Distance,'
                        . 'b.minDistancePerDay as Min_Distance_Per_Day,'
                        . 'b.minHrPerDay as Min_Hour_Per_Day,'
                        . 'b.extraKmRate as Extra_Km_Rate,'
                        . 'b.extraKmRateOw as Extra_Km_Rate_One_Way,'
                        . 'b.extraHrRate as Extra_Hr_Rate,'
                        . 'b.waitingChargesPerHr as Waiting_Charges_Per_Hr,'
                        // . 'b.ttlWaitingHrs as Total_Waiting_Hrs,'
                        . 'b.hillCharges as Hill_Charges,'
                        . 'b.driverAllowance as Driver_Allowance,'
                        . 'b.tollCharges as Toll_Charges,'
                        . 'b.parkingCharges as Parking_Charges,'
                        . 'b.stateCharges as State_Charges,'
                        . 'b.otherCharges as Other_Charges,'
                        . 'b.waitingTimeDurationCharges as Waiting_Time_Charges,'
                        . 'b.extraKms as Extra_Kms,'
                        . 'b.extraHrs as Extra_Hrs,'
                        . 'b.extraDay as Extra_Day,'
                        // . 'b.ttlWaitingTime as Total_Waiting_Time,'
                        // . 'b.stdWaitingTime as Sandard_Waiting_Time,'
                        . 'b.nightHalt as Night_Halt,'
                        . 'b.nightCharges as Night_Charges,'
                        . 'b.basicFare as Full_Day,'
                        . 'b.basicFare as Half_Day,'
                        . 'b.basicFare as Transfer,'
                        . 'b.serviceTax as Service_Tax,'
                        . 'b.totalBasicFare as Total_Approx_Fare'
                )
                ->add('from', 'Booking\Entity\Bookingadd b')
                ->innerJoin('\Booking\Entity\Bookingtraveller', 'bt', Join::WITH, 'b.bookingId = bt.bookingId')
                ->where('b.bookingId=' . $root);

        $data = $queryBuilder->getQuery()->getArrayResult();
        //print_r($data);
        $bookingId = "";
        $status = "";
        $uId = "";
        $ref_No = "";
        $Travel_Date = "";
        $Pickup_Time = "";
        $No_Of_Days = "";
        $Pickup_City = "";
        $Destination_City = "";
        $Booking_DateTime = "";
        $Vehicle_Id = "";
        $KM_Rate = "";
        $HR_Rate = "";
        $KM_Rate_One_way = "";
        $No_Of_Cars = "";
        $Travel_Root = "";
        $Approx_Distance = "";
        $Min_Distance_Per_Day = "";
        $Min_Hour_Per_Day = "";
        $Driver_Allowance = "";
        $Other_Charges = "";
        $Night_Halt = "";
        $Night_Charges = "";
        $State_Charges = "";
        $Hill_Charges = "";
        $Service_Tax = "";
        $Full_Day = "";
        $Half_Day = "";
        $Transfer = "";
        $Total_Approx_Fare = "";


        //  $str = ("bookingId Travel_Date Pickup_Time No_Of_Days Pickup_City  Destination_City Vehicle_Name No_Of_Cars Travel_Root Other_Requirement Travel_Type Booking_DateTime travellerName travellerEmailId travellerContactNo travellerPickupAddress landMark KM_Rate");
        // $str2 = ("KM_Rate_One_way Approx_Distance Min_Distance_Per_Day Driver_Allowance  Night_Halt Night_Charges Total_Approx_Fare Other_Charges  State_Charges  Hill_Charges Service_Tax");
        // print_r($data); die;
        foreach ($data[0] as $key => $value) {

            //  echo $value; 

            if ($value != '') {

                switch ($key) {

                    case "bookingId":
                        $bookingId = $value;
                        break;

                    case "uId":
                        $uId = $value;
                        break;

                    case "ref_No":
                        $ref_No = $value;
                        break;

                    case "Travel_Date":
                        $Travel_Date = $value;
                        break;

                    case "Travel_End_Date":
                        $Travel_End_Date = $value;
                        break;

                    case "Pickup_Time":
                        $Pickup_Time = $value;
                        break;

                    case "Travel_End_Time":
                        $Travel_End_Time = $value;
                        break;

                    case 'No_Of_Days':
                        $No_Of_Days = $value;
                        break;

                    case 'Booking_DateTime':
                        $Booking_DateTime = $value;
                        break;

                    case 'Pickup_City':
                        $Pickup_City = $value;
                        break;

                    case 'Destination_City':
                        $Destination_City = $value;
                        break;

                    case 'Vehicle_Id':
                        $Vehicle_Id = $value;
                        break;

                    case 'KM_Rate':
                        $KM_Rate = floatval($value);

                        break;

                    case 'HR_Rate':
                        $HR_Rate = floatval($value);

                        break;

                    case 'KM_Rate_One_way':
                        $KM_Rate_One_way = floatval($value);

                        break;

                    case 'No_Of_Cars':
                        $No_Of_Cars = $value;

                        break;

                    case 'Travel_Root':
                        $Travel_Root = $value;
                        break;


                    case "Approx_Distance":
                        $Approx_Distance = $value;
                        break;

                    case "Min_Distance_Per_Day":
                        $Min_Distance_Per_Day = $value;
                        break;


                    case "Min_Hour_Per_Day":
                        $Min_Hour_Per_Day = $value;
                        break;

                    case "Driver_Allowance":
                        $Driver_Allowance = $value;
                        break;

                    case "Other_Charges":
                        $Other_Charges = $value;
                        break;
                    case "Night_Halt":
                        $Night_Halt = $value;
                        break;
                    case "Night_Charges":
                        $Night_Charges = $value;
                        break;

                    case "Total_Approx_Fare":
                        $Total_Approx_Fare = $value;
                        break;

                    case "Other_Charges":
                        $Other_Charges = $value;
                        break;

                    case "State_Charges":
                        $State_Charges = $value;
                        break;
                    case "Hill_Charges":
                        $Hill_Charges = $value;
                        break;
                    case "Service_Tax":
                        $Service_Tax = $value;
                        break;

                    case "Full_Day":
                        $Full_Day = $value;
                        break;

                    case "Half_Day":
                        $Half_Day = $value;
                        break;

                    case "Transfer":
                        $Transfer = $value;
                        break;
                }
            }

            $KM_Limit = ($Approx_Distance > ($Min_Distance_Per_Day * $No_Of_Days)) ? $Approx_Distance : ($Min_Distance_Per_Day * $No_Of_Days);



            //html2 += '<span class="cabsaasPopupCommonLabelDiv1">' + key + ':</span> <span class="cabsaasPopupCommonValueDiv1"> ' + value + '</span>';
        }

        // ROUNDTRIP AND MULTICITY

        $customArray["id"] = $id;
        

       
  // Share Cab
        if ($Travel_Root == '11') {

            $basicFare = $Total_Approx_Fare;
            $totalCharges = ($basicFare + $Other_Charges) * $No_Of_Cars * $No_Of_Days;
            // $serviceTaxApllied = ceil((($totalCharges) * $Service_Tax) / 100);
            $serviceTaxApllied = 0;
            $totalFare = ceil($totalCharges + $serviceTaxApllied);
            $customArray["bookedFare"] = $totalFare ;

            $em = $this->getEntityManager();
            $queryBuilder = $em->createQueryBuilder();

            $queryBuilder->add('select', ' obj.basicFare, cmpny.companyName, cmpny.ownerContactNo, cmpny.companyOwner, cmpny.userid')
                    ->add('from', 'Tariff\Entity\Sharecab obj')
                    ->innerJoin('\User\Entity\Signup', 'user', Join::WITH, 'user.uId = obj.uId And (user.subuserid=5 OR user.subuserid=1)')
                    ->innerJoin('\User\Entity\Company', 'cmpny', Join::WITH, 'cmpny.userid = obj.uId')
                    ->where('obj.vehicleId=' . $Vehicle_Id, 'obj.sCityId=' . $Pickup_City);

            $data1 = $queryBuilder->getQuery()->getArrayResult();
//print_r($Vehicle_Id);print_r($Pickup_City);die;



            for ($i = 0; $i < sizeof($data1); $i++) {

                $vendorId = $data1[$i]['userid'];
                $basicFare = $data1[$i]['basicFare'];
                $companyName = $data1[$i]['companyName'];
                $ownerContactNo = $data1[$i]['ownerContactNo'];
                $companyOwner = $data1[$i]['companyOwner'];

                $basicFare = intval($basicFare);
                $totalCharges = ($basicFare + $Other_Charges) * $No_Of_Cars * $No_Of_Days;
                // $serviceTaxApllied = ceil((($totalCharges) * $Service_Tax) / 100);
                $serviceTaxApllied = 0;
                $totalFareVendor = ceil($totalCharges + $serviceTaxApllied);

                $customArray["uId"] = $vendorId;
               $customArray["bookingId"] = $bookingId;
                $customArray["companyName"] = $companyName;
                $customArray["ownerContactNo"] = $ownerContactNo;
                $customArray["companyOwner"] = $companyOwner;
                $customArray["totalFareVendor"] = $totalFareVendor;
                $customArray["FareDiff"] = $totalFare - $totalFareVendor;
                $customArray["transfer"] = $basicFare;  
                $customArray["otherCharges"] = $Other_Charges;
                $customArray["approxDistance"] = $Approx_Distance;

                $myArr[] = $customArray;
            }
        }
   //Deal
     if ($Travel_Root == '12' ) {

            $arr = Array();
            $basicFare = $Total_Approx_Fare;
//            $driverAllowance = $Driver_Allowance * $No_Of_Days;
//            $nightHaltCharges = $Night_Halt * ($No_Of_Days - 1);
//            $oshCharges = $Other_Charges + $State_Charges + $Hill_Charges;
            $totalCharges = $Total_Approx_Fare;
            $serviceTaxApllied = ceil(($totalCharges * $Service_Tax) / 100);
            $totalFare = ceil($totalCharges + $serviceTaxApllied);
            $customArray["bookedFare"] = $Total_Approx_Fare;

            $em = $this->getEntityManager();
            $queryBuilder = $em->createQueryBuilder();

            $queryBuilder->add('select',  'dl.sCityId,'
                    . ''
                    . 'dl.vehicleId,'                    
                    . 'dl.slotIdFrom,'
                    . 'dl.extKm,'
                    . 'dl.dealFare,'
                    . 'dl.basicFare,'
                    . 'dl.tDate,'
                    . 'dl.uId,'
                    . 'cmpny.companyName,'
                    . 'cmpny.ownerContactNo,'
                    . 'cmpny.companyOwner, '
                    . 'cmpny.userid')
                    ->add('from', 'Tariff\Entity\Deals dl')
                    ->innerJoin('\User\Entity\Signup', 'user', Join::WITH, 'user.uId = dl.uId And (user.subuserid=5 OR user.subuserid=1)')
                    ->innerJoin('\User\Entity\Company', 'cmpny', Join::WITH, 'cmpny.userid = dl.uId')
                     ->innerJoin('\Admin\Entity\Location', 'l', Join::WITH, 'l.locationId=dl.pickLocId')
                    ->where('dl.vehicleId=' . $Vehicle_Id, 'l.ctid=' . $Pickup_City);

            $data1 = $queryBuilder->getQuery()->getArrayResult();
//print_r($data1);die;



          for ($i = 0; $i < sizeof($data1); $i++) {

                $vendorId = $data1[$i]['userid'];
                $basicFare = $data1[$i]['dealFare'];
                $companyName = $data1[$i]['companyName'];
                $ownerContactNo = $data1[$i]['ownerContactNo'];
                $companyOwner = $data1[$i]['companyOwner'];

                $basicFare = intval($basicFare);
                $totalCharges = ($basicFare + $Other_Charges) * $No_Of_Cars * $No_Of_Days;
                // $serviceTaxApllied = ceil((($totalCharges) * $Service_Tax) / 100);
                $serviceTaxApllied = 0;
                $totalFareVendor = ceil($totalCharges + $serviceTaxApllied);

                $customArray["uId"] = $vendorId;
                $customArray["refNo"] = $ref_No;
                 $customArray["bookingId"] = $bookingId;
                $customArray["companyName"] = $companyName;
                $customArray["ownerContactNo"] = $ownerContactNo;
                $customArray["companyOwner"] = $companyOwner;
                $customArray["totalFareVendor"] = $totalFareVendor;
                $customArray["FareDiff"] = $totalFare - $totalFareVendor;
                $customArray["transfer"] = $basicFare;
                $customArray["otherCharges"] = $Other_Charges;
                $customArray["approxDistance"] = $Approx_Distance;

                $myArr[] = $customArray;
            }
        }
	//	print_r($myArr);die;
        return $myArr;
    }  
}
