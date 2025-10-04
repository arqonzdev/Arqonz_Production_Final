<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace App\Controller;

use App\EventListener\AuthenticationLoginListener;
use App\Form\LoginFormType;
use App\Form\PasswordMaxLengthTrait;
use App\Form\RegistrationFormHandler;
use App\Form\AgentRegistrationFormHandler;
use App\Form\RegistrationFormType;
use App\Form\EventRegistrationFormType;

use App\Form\ProRequirementFormType;
use App\Form\AgentRegistrationFormType;
use App\Form\partnerRegistrationFormType;
use App\Form\AgentUserRegistrationFormType;
use App\Model\Customer;
use App\Services\NewsletterDoubleOptInService;
use App\Services\PasswordRecoveryService;
use CustomerManagementFrameworkBundle\CustomerProvider\CustomerProviderInterface;
use CustomerManagementFrameworkBundle\CustomerSaveValidator\Exception\DuplicateCustomerException;
use CustomerManagementFrameworkBundle\Model\CustomerInterface;
use CustomerManagementFrameworkBundle\Security\Authentication\LoginManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\CustomerObject;
use Pimcore\DataObject\Consent\Service;
use Pimcore\Model\DataObject\Service as DataObjectService;
// use Pimcore\Model\DataObject\Service;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Pimcore\Model\DataObject\ArchitectProjects;
use Pimcore\Model\DataObject\BuilderProjects;
use Pimcore\Model\DataObject\ProNotification;
use Pimcore\Model\DataObject\ProRequirement;
use Pimcore\Model\DataObject\EmailTemplate;
use Pimcore\Model\DataObject\Data\StructuredTable;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Website\Tool\Text;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Pimcore\Mail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use App\Form\TestLoginFormType;
use App\Form\MobileRegistrationFormType;
use App\Form\MobileRegistrationFormHandler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Uid\Uuid;



/**
 * Class AccountController
 *
 * Controller that handles all account functionality, including register, login and connect to SSO profiles
 */
class AccountController extends BaseController
{
    use PasswordMaxLengthTrait;
    /**
     * @Route("/account/login", name="account-login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function loginAction(
        AuthenticationUtils $authenticationUtils,
        Request $request,
        UserInterface $user = null
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $formData = [
            '_username' => $lastUsername
        ];

        $form = $this->createForm(LoginFormType::class, $formData, [
            'action' => $this->generateUrl('account-login'),
        ]);

        //store referer in session to get redirected after login
        if (!$request->get('no-referer-redirect')) {
            $request->getSession()->set('_security.demo_frontend.target_path', $request->headers->get('referer'));
        }

        return $this->render('account/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error ? 'Credentials are not valid.' : '',
            'hideBreadcrumbs' => true
        ]);
    }


    // For Mobile App
    
    /**
     * @Route("/login-with-password", name="login-with-password", methods={"POST"})
     */
    public function loginWithPassword(
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['username'] ?? null;
            $password = $data['password'] ?? null;

            if (!$email || !$password) {
                return new JsonResponse(['success' => false, 'message' => 'Email and password are required'], 400);
            }

            // Find customer
            $customerList = new \Pimcore\Model\DataObject\Customer\Listing();
            $customerList->addConditionParam("email = ?", $email);
            $customers = $customerList->load();

            if (empty($customers)) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            $customer = $customers[0];

            // Verify password - Custom implementation for Customer objects
            if (!$this->verifyCustomerPassword($customer, $password)) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            $logger->info("Password login successful for user: " . $email);
            $customerType = $customer->getcustomertype();
            $customerID = $customer->getUserID();

            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $customer->getKey(),
                    'email' => $customer->getemail(),
                    'name' => $customer->getfirstname(),
                    'customerType' => $customerType,
                    'customerID' => $customerID,
                    'subscriptionStart' => $customer->getSubscriptionStart() ? $customer->getSubscriptionStart()->format('Y-m-d') : null,
                    'phoneCountry' => $customer->getPhoneCountry(),
                ]
            ]);

        } catch (\Exception $e) {
            $logger->error("Password login error: " . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * Custom password verification for Customer objects
     */
    private function verifyCustomerPassword(Customer $customer, string $password): bool
    {
        // Get the hashed password from your Customer object
        $hashedPassword = $customer->getPassword(); // Make sure this matches your field name
        
        // If password is not hashed (plain text - not recommended), compare directly
        // return $hashedPassword === $password;
        
        // For hashed passwords (recommended):
        return password_verify($password, $hashedPassword);
    }


    use PasswordMaxLengthTrait;
    /**
     * @Route("/account/sign-in", name="account-login-test")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param UserInterface|null $user
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param UrlGeneratorInterface $urlGenerator
     * 
     * 
     *
     * @return Response|RedirectResponse
     */
    public function testloginAction(
        AuthenticationUtils $authenticationUtils,
        Request $request,
        UserInterface $user = null,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        Security $security,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        SessionInterface $session
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {


            return $this->redirectToRoute('account-index');


        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        

        $formData = [
            '_username' => $lastUsername
        ];

        // $form = $this->createForm(TestLoginFormType::class, $formData, [
        //     'action' => $this->generateUrl('account-login'),
        // ]);

        $form = $this->createForm(TestLoginFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $username = $form->get('_username')->getData();
            $OTP = $form->get('_otp')->getData();

            $logger->info("OTP Entered ". $OTP);
            

            $CustomersList = new \Pimcore\Model\DataObject\Customer\Listing();
            $CustomersList->addConditionParam("email = ?", $username);
            $Customers = $CustomersList->load();
            
            if (empty($Customers)) {
                $logger->error('Customer not found for email', ['email' => $username]);
                $this->addFlash('error', 'Email does not exist in our system. Please check your email address or register for a new account.');
                return $this->render('account/login.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Email does not exist in our system.',
                    'hideBreadcrumbs' => true
                ]);
            }
            
            $Customer = $Customers[0];
            $logger->info("OTP From Database ". $Customer-> getOtp());

            $userId = $Customer->getUserID();

            if ((int) $Customer->getOtp() === (int) $OTP) {
                // OTP verification success
                $logger->info("OTP Verification Status: Success");
                $response = $this->redirectToRoute('account-index');
                $loginManager->login($Customer, $request, $response);
            
                // Redirect to target route
                if ($Customer->getEmailVerified() !== 'true') {
                    return $this->redirectToRoute('Account-Verification-OTP', ['url' => $userId]);
                }
                if ($Customer->getPhoneVerified() !== 'True') {
                    return $this->redirectToRoute('Account-Verification-OTP', ['url' => $userId]);
                }
                return $this->redirectToRoute('account-index');
            } else {
                // OTP verification failed, set an error message
                $this->addFlash('error', 'Invalid OTP. Please try again.');
            }


            
        }

        //store referer in session to get redirected after login
        if (!$request->get('no-referer-redirect')) {
            $request->getSession()->set('_security.demo_frontend.target_path', $request->headers->get('referer'));
        }

        return $this->render('account/loginTest.html.twig', [
            'form' => $form->createView(),
            'error' => $error ? 'Credentials are not valid.' : '',
            'hideBreadcrumbs' => true
        ]);
    }


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/add-agent", name="Add-Agent")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function AddAgentAction(
        Request $request,
        Security $security,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        $User = $security->getUser();
        $CUstomer = $User;

        // Fetch all Channel Partners
        $channelPartnersList = new \Pimcore\Model\DataObject\Customer\Listing();
        $channelPartnersList->addConditionParam("customertype = ?", "ChannelPartner");
        $channelPartners = $channelPartnersList->load();

        // Prepare the data for the dropdown
        $channelPartnersData = [];
        foreach ($channelPartners as $partner) {
            $channelPartnersData[$partner->getPartnerID() . ' - ' . $partner->getFirstname()] = $partner->getPartnerID();
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(AgentRegistrationFormType::class, $formData, ['hidePassword' => false, 'channelPartners' => $channelPartnersData]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setcustomertype("Agent");

                        // Generate and assign a unique Agent ID
                        $uniqueAgentId = '';
                        do {
                            $uniqueAgentId = $this->generateUniqueAgentId($logger);
                        } while (!$this->isPartnerIDUnique($uniqueAgentId, $logger));

                        // Assign the unique AgentID
                        $customer->setAgentID($uniqueAgentId);

                        $ChannelPartnerID = $form->getData()['AgentID'];
                        
                        $ChannelPartnersList = new \Pimcore\Model\DataObject\Customer\Listing();
                        $ChannelPartnersList->addConditionParam("PartnerID = ?", $ChannelPartnerID);

                        $ChannelPartners = $ChannelPartnersList->load();
                        
                        $ChannelPartner = $ChannelPartners[0];



                        $customer->setChannelPartner($ChannelPartner);
                        $customer->setRefferedByChannelPartner('1');
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                        $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        // $custuserID = $customer->getUserID();
                        $this->addFlash('success', $translator->trans('Agent successfully Added'));
                        return $this->redirectToRoute('account-index');

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
            $this->addFlash('success', $translator->trans('Agent successfully Added'));
            return $this->redirectToRoute('account-index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $this->render('Professional/Dashboard/dashboard_add_Agent.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false
        ]);
    }

    // 
    private function generateUniquePartnerID(LoggerInterface $logger): string {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $generatedID = substr(str_shuffle($characters), 0, 6);
        // $logger->info("Generated Partner ID: {$generatedID}");
        return $generatedID;
    }
    
    private function isPartnerIDUnique(string $PartnerID, LoggerInterface $logger): bool {
        $list = new \Pimcore\Model\DataObject\Customer\Listing();
        $list->addConditionParam('PartnerID = ?', $PartnerID);
        $count = count($list);
        // $logger->info("Checked uniqueness for Partner ID: {$PartnerID}. Count found: {$count}");
        return $count === 0;
    }


    private function generateUniqueAgentID(LoggerInterface $logger): string {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($characters), 0, 6);
    }
    
    private function isAgentIDUnique(string $AgentID, LoggerInterface $logger): bool {
        $list = new \Pimcore\Model\DataObject\Customer\Listing();
        $list->addConditionParam('AgentID = ?', $AgentID);
        return count($list) === 0;
    }


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/add-channel-partner", name="Add-channel-partner")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function AddChannelPartnerAction(
        Request $request,
        Security $security,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session,
    ) {

        $User = $security->getUser();

        $CUstomer = $User;


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(partnerRegistrationFormType::class, $formData, ['hidePassword' => false]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setcustomertype("ChannelPartner");

                        // $logger->info("Starting Channel Partner addition process");

                        // Generate and assign a unique Agent ID
                        $uniquePartnerID = '';
                        do {
                            $uniquePartnerID = $this->generateUniquePartnerID($logger);
                        } while (!$this->isPartnerIDUnique($uniquePartnerID, $logger));

                        // $logger->info("Final Unique Partner ID: {$uniquePartnerID}");

                        // Assign the unique AgentID
                        $customer->setPartnerID($uniquePartnerID);


                        $customer->setChannelPartner($User);
                        
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                        $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        // $custuserID = $customer->getUserID();
                        $this->addFlash('success', $translator->trans('Channel Partner successfully Added'));
                        return $this->redirectToRoute('account-index');

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
            $this->addFlash('success', $translator->trans('Channel Partner successfully Added'));
            return $this->redirectToRoute('account-index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $this->render('Professional/Dashboard/dashboard_add_ChannelPartner.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false
        ]);
    }


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/Agent-add-user", name="Agent-Add-user")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function AgentAddUserAction(
        Request $request,
        Security $security,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        $User = $security->getUser();
        $CUstomer = $User;
        $AgentID = $CUstomer->getAgentID();


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(AgentUserRegistrationFormType::class, $formData, ['hidePassword' => false, 'AgentID' => $AgentID,]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);

                        $agentID = $AgentID;

                        if (!empty($agentID)) {
                            $agentList = new \Pimcore\Model\DataObject\Customer\Listing();
                            $agentList->addConditionParam("AgentID = ?", $agentID);
                        
                            $agents = $agentList->load();
                        
                            if (!empty($agents)) {
                                $agent = $agents[0];
                                $customer->setAgentAccount($agent);
                            } else {
                                // Optional: Log a warning if the agent ID doesn't match any record
                                $this->logger->warning("No agent found for AgentID: {$agentID}");
                            }
                        }

                        
                        $customer->setChannelPartner($User->getChannelPartner());
                        $customer->setRefferedByAgent($User);
                        $customer->setRefferedByAgent('1');
                        $customer->setRefferedByChannelPartner('1');
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                        $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        // $custuserID = $customer->getUserID();
                        $this->addFlash('success', $translator->trans('Agent successfully Added'));
                        return $this->redirectToRoute('account-index');

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
            $this->addFlash('success', $translator->trans('Agent successfully Added'));
            return $this->redirectToRoute('account-index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $this->render('Professional/Dashboard/dashboard_add_User.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'AgentID' => $AgentID

        ]);
    }



    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/register", name="account-register")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function registerAction(
        Request $request,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }

        // Fetch all Channel Partners
        $channelPartnersList = new \Pimcore\Model\DataObject\Customer\Listing();
        $channelPartnersList->addConditionParam("customertype = ?", "Agent");
        $Agents = $channelPartnersList->load();

        // Prepare the data for the dropdown
        $AgentsData = [];
        foreach ($Agents as $partner) {
            $AgentsData[$partner->getAgentID() . ' - ' . $partner->getFirstname()] = $partner->getAgentID();
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(RegistrationFormType::class, $formData, ['hidePassword' => false, 'Agents' => $AgentsData]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setCreditPoints(1);

                        // // Temporary Mobile Verification Auto
                        // $customer->setPhoneVerified("True");
                        // Temporary mobile verification End
                        $agent = null;

                        $agentID = $form->getData()['AgentID'] ?? null;

                        if (!empty($agentID)) {
                            $agentList = new \Pimcore\Model\DataObject\Customer\Listing();
                            $agentList->addConditionParam("AgentID = ?", $agentID);
                        
                            $agents = $agentList->load();
                        
                            if (!empty($agents)) {
                                $agent = $agents[0];
                                $customer->setAgentAccount($agent);
                            } else {
                                // Optional: Log a warning if the agent ID doesn't match any record
                                $this->logger->warning("No agent found for AgentID: {$agentID}");
                            }
                        }

                        $customer->setAgentAccount($agent);

                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);
                        $customerfirstName = $customer->getfirstname();

                        $WelcomeMessage = "Hey ".$customerfirstName.", Thank you for signing up on Arqonz, your account is successfully created. We're stoked to have you join our crew of construction wizards – architects, engineers, manufacturers, the whole gang! See you around the build zone!";
                        
                        $this->sendNEWWhatsAppMessage($customer->getPhone(), $WelcomeMessage);

                        // $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        // $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);

                        // $whatsAppMessage = "*{$Phoneotp}* is your verification code. For your security, do not share this code.";
                        $logger->info('About to send WhatsApp OTP via Meta API', [
                            'phone' => $customer->getPhone(),
                            'otp' => $Phoneotp
                        ]);
                        $this->sendWhatsAppOTPWithMeta($customer->getPhone(), $Phoneotp, $logger);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);
                        $loginManager->login($customer, $request, null);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        $custuserID = $customer->getUserID();

                        return $this->redirectToRoute('Account-Verification-OTP', ['url' => $custuserID]);

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        // Get reCAPTCHA configuration
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        
        return $this->render('account/register.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'recaptcha_site_key' => $recaptchaConfig['site_key']
        ]);
    }
    


    private function sendNEWWhatsAppMessage(string $phoneNumber, string $message): void
    {
        // Get WhatsApp API configuration from environment
        $whatsappConfig = \App\Service\EnvironmentConfigService::getWhatsAppConfig();
        $apiUrl = $whatsappConfig['api_url'];
        $apiKey = $whatsappConfig['api_key'];
        
        if (empty($apiKey) || empty($apiUrl)) {
            throw new \Exception('WhatsApp API credentials not configured');
        }
        
        // Format the phone number to include the @c.us suffix (correct format for WhatsApp Web API)
        $chatId = $phoneNumber;
        if (!str_ends_with($phoneNumber, '@c.us')) {
            $chatId = '91'.$phoneNumber . '@c.us';  // Changed from @s.whatsapp.net to @c.us
        }
        
        // Prepare the request data
        $requestData = [
            'chatId' => $chatId,
            'contentType' => 'string',
            'content' => $message
        ];
        
        // Initialize cURL
        $ch = curl_init($apiUrl);
        
        // Set cURL options with timeout
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 seconds timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 seconds connection timeout
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,  // Changed from api_key to x-api-key
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        
        // Add verbose debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Get verbose information
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Log request details
        error_log('WhatsApp API Request to: ' . $apiUrl);
        error_log('WhatsApp API Request data: ' . json_encode($requestData));
        error_log('WhatsApp API Response code: ' . $httpCode);
        error_log('WhatsApp API Response: ' . $response);
        error_log('Verbose log: ' . $verboseLog);
        
        // Handle errors if needed
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            error_log('WhatsApp API Error: ' . $error_msg);
            throw new \Exception("WhatsApp API request failed: " . $error_msg);
        }
        
        // Check for non-200 status codes
        if ($httpCode !== 200) {
            error_log('WhatsApp API Error: Non-200 status code received: ' . $httpCode);
            throw new \Exception("WhatsApp API request failed with status code: " . $httpCode);
        }
        
        // Try to decode response to check for API-level errors
        $responseData = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($responseData['error'])) {
            error_log('WhatsApp API Error: ' . $responseData['error']);
            throw new \Exception("WhatsApp API error: " . $responseData['error']);
        }
        
        // Close the cURL session
        curl_close($ch);
    }



    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("account/register-mobile", name="account_register_mobile", methods={"POST"})
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param MobileRegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function registerMobile(
        Request $request,
        MobileRegistrationFormHandler $registrationFormHandler,
        CustomerProviderInterface $customerProvider,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
        // Decode the JSON data from the request
        $data = json_decode($request->getContent(), true);
    
        // Validate the JSON data
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid JSON data'], 400);
        }
    
        // Create a new customer instance
        // $customer = new Customer();
        // $customer->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/customers/--/--/'));

        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();
    
        // Prepare form data
        $formData = [
            'firstname' => $data['firstName'] ?? null,
            'lastname' => $data['lastName'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'] ?? null,
            'customertype' => $data['profession'] ?? 'Professional',
            'AgentID' => $data['agentId'] ?? null,
        ];
    
        // Create and submit the form
        $form = $this->createForm(MobileRegistrationFormType::class, $customer);
        
        // Use submit with the second parameter as false to merge data into the existing customer object
        // instead of replacing it
        $form->submit($formData, false);
    
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }
    
        // The form has already populated the customer object, so we don't need to do much here
        // but we still call updateCustomerFromForm for consistency and to handle any special cases
        $registrationFormHandler->updateCustomerFromForm($customer, $form);
    
        // Validate the customer object
        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errorMessages], 400);
        }
    
        // Set additional fields
        $customer->setActive(true);
        $customer->setCustomerLanguage($request->getLocale());

        $timestamp = time();
        $randomDigit = rand(0, 9);
        $userID = $timestamp . $randomDigit;
        $customer->setUserID(Text::toUrl($userID));
        $customer->setPS($data['password']);
        $Emailtoken = bin2hex(random_bytes(32));
        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
        $Phonetoken = bin2hex(random_bytes(32));
        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
        $Emailotp = random_int(100000, 999999);
        $customer->setEmailVerificationOTP($Emailotp);
        $Phoneotp = random_int(100000, 999999);
        $customer->setMobileVerificationOTP($Phoneotp);

        // Temporary Mobile Verification Auto
        $customer->setPhoneVerified("True");
        // Temporary mobile verification End


        $agentID = $data['agentId'] ?? null;

        if (!empty($agentID)) {
            $agentList = new \Pimcore\Model\DataObject\Customer\Listing();
            $agentList->addConditionParam("AgentID = ?", $agentID);
        
            $agents = $agentList->load();
        
            if (!empty($agents)) {
                $agent = $agents[0];
                $customer->setAgentAccount($agent);
            } else {
                // Optional: Log a warning if the agent ID doesn't match any record
                $this->logger->warning("No agent found for AgentID: {$agentID}");
            }
        }

        
    
        // Save the customer
        try {
            $customer->save();
            $Notification = new ProNotification();
            $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
            $Notification->setDescription("Click to Open Submit Details.");
            $Notification->setCustomer($customer);
            $redirecturl = '/account/Profile';

            $Notification->seturl($redirecturl);
            $Notification->setPublished(true);
            $Notification->save();
            $plainPassword = $form->get('password')->getData();
            $this->sendWelcomeEmail($customer);
            $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

            $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
            $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

            $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
            $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);

            return new JsonResponse([
                'success' => true, 
                'message' => 'Registration successful! Please verify your email and phone.',
                'userId' => $customer->getUserID(), // Add this line to send the userId back to the app
                'profession' => $customer->getcustomertype(),
            ]);
        } catch (\Exception $e) {
            $logger->error('Registration failed: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/register/1710257976", name="Murugan-account-register")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function muruganregisterAction(
        Request $request,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(RegistrationFormType::class, $formData, ['hidePassword' => false]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setRefferedBy('Muruga Rathinam');
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                        $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        $custuserID = $customer->getUserID();

                        return $this->redirectToRoute('Account-Verification-OTP', ['url' => $custuserID]);

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        // Get reCAPTCHA configuration
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        
        return $this->render('account/register.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'recaptcha_site_key' => $recaptchaConfig['site_key']
        ]);
    }


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/register/1711075589", name="John-account-register")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function JohnregisterAction(
        Request $request,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(RegistrationFormType::class, $formData, ['hidePassword' => false]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setRefferedBy('John Benner');
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                
                        
                        $WelcomeMessage= "Hey Vijay, Thank you for signing up on Arqonz, your account is successfully created. We're stoked to have you join our crew of construction wizards – architects, engineers, manufacturers, the whole gang!  See you around the build zone!";

                        $this->sendNEWWhatsAppMessage($Customer->getPhone(), $whatsAppMessage);
                        
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        $custuserID = $customer->getUserID();

                        return $this->redirectToRoute('Account-Verification-OTP', ['url' => $custuserID]);

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        // Get reCAPTCHA configuration
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        
        return $this->render('account/register.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'recaptcha_site_key' => $recaptchaConfig['site_key']
        ]);
    }


    // Account Verification Controllers for Mobile

    /**
     * @Route("/account-verification-status/{userId}", name="account_verification_status", methods={"GET"})
     */
    public function verificationStatusAction(string $userId, LoggerInterface $logger): JsonResponse
    {
        try {
            // Find the customer by UserID
            $customerList = new \Pimcore\Model\DataObject\Customer\Listing();
            $customerList->addConditionParam("UserID = ?", $userId);
            $customers = $customerList->load();
            
            if (empty($customers)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            $customer = $customers[0];
            
            // Return verification status
            return new JsonResponse([
                'success' => true,
                'data' => [
                    'emailVerified' => $customer->getEmailVerified(),
                    'phoneVerified' => $customer->getPhoneVerified(),
                    'customerType' => $customer->getCustomertype()
                ]
            ]);
        } catch (\Exception $e) {
            $logger->error('Error checking verification status: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse([
                'success' => false,
                'message' => 'Error checking verification status'
            ], 500);
        }
    }


    /**
     * @Route("/verify-account-otp", name="verify_account_otp", methods={"POST"})
     */
    public function verifyOtpAction(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Decode JSON request
            $data = json_decode($request->getContent(), true);
            
            // Validate request data
            if (!isset($data['userId']) || 
                (!isset($data['emailOtp']) && !isset($data['phoneOtp']))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ], 400);
            }
            
            // Find the customer
            $customerList = new \Pimcore\Model\DataObject\Customer\Listing();
            $customerList->addConditionParam("UserID = ?", $data['userId']);
            $customers = $customerList->load();
            
            if (empty($customers)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            $customer = $customers[0];
            $emailVerified = $customer->getEmailVerified() === 'true';
            $phoneVerified = $customer->getPhoneVerified() === 'True';
            
            // Verify Email OTP if provided and not already verified
            if (!$emailVerified && isset($data['emailOtp']) && $data['emailOtp']) {
                $systemEmailOtp = $customer->getEmailVerificationOTP();
                if ((int)$data['emailOtp'] === (int)$systemEmailOtp) {
                    $customer->setEmailVerified('true');
                    $customer->setEmailVerificationOTP(null); // Clear OTP
                    $emailVerified = true;
                }
            }
            
            // Verify Phone OTP if provided and not already verified
            if (!$phoneVerified && isset($data['phoneOtp']) && $data['phoneOtp']) {
                $systemPhoneOtp = $customer->getMobileVerificationOTP();
                if ((int)$data['phoneOtp'] === (int)$systemPhoneOtp) {
                    $customer->setPhoneVerified('True');
                    $customer->setMobileVerificationOTP(null); // Clear OTP
                    $phoneVerified = true;
                }
            }
            
            // Save customer if any verification was successful
            if ($customer->getEmailVerified() === 'true' || $customer->getPhoneVerified() === 'True') {
                $customer->save();
            }
            
            return new JsonResponse([
                'success' => true,
                'data' => [
                    'emailVerified' => $emailVerified,
                    'phoneVerified' => $phoneVerified,
                    'customerType' => $customer->getCustomertype()
                ]
            ]);
        } catch (\Exception $e) {
            $logger->error('Error verifying OTP: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse([
                'success' => false,
                'message' => 'Error verifying OTP'
            ], 500);
        }
    }

    /**
     * @Route("/resend-account-otp", name="resend_account_otp", methods={"POST"})
     */
    public function resendOtpAction(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Decode JSON request
            $data = json_decode($request->getContent(), true);
            
            // Validate request data
            if (!isset($data['userId']) || !isset($data['type'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ], 400);
            }
            
            // Find the customer
            $customerList = new \Pimcore\Model\DataObject\Customer\Listing();
            $customerList->addConditionParam("UserID = ?", $data['userId']);
            $customers = $customerList->load();
            
            if (empty($customers)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            $customer = $customers[0];
            
            // Generate and send OTP based on type
            if ($data['type'] === 'email') {
                // Only resend if not already verified
                if ($customer->getEmailVerified() !== 'true') {
                    $otp = random_int(100000, 999999);
                    $customer->setEmailVerificationOTP($otp);
                    $customer->save();
                    
                    // Send OTP via Email
                    $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                    $EmailTemplates->addConditionParam("TemplateName = ?", "EmailOTPVerification");
                    $EmailTemplate = $EmailTemplates->load()[0];

                    $subject = $EmailTemplate->getSubject();
                    $htmlContent = str_replace(
                        ["[customername]", "[OTP]"],
                        [$customer->getfirstname(), $otp],
                        $EmailTemplate->getContent()
                    );

                    $mail = new \Pimcore\Mail();
                    $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                    $mail->to($customer->getEmail());
                    $mail->subject($subject);
                    $mail->html($htmlContent);
                    $mail->send();
                    
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Email OTP sent successfully'
                    ]);
                } else {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Email already verified'
                    ]);
                }
            } elseif ($data['type'] === 'phone') {
                // Only resend if not already verified
                if ($customer->getPhoneVerified() !== 'True') {
                    $Phoneotp = random_int(100000, 999999);
                    $customer->setMobileVerificationOTP($Phoneotp);
                    $customer->save();
                    
                    // Send OTP via WhatsApp
                    $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                    $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);
                    
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Phone OTP sent successfully'
                    ]);
                } else {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Phone already verified'
                    ]);
                }
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid OTP type'
                ], 400);
            }
        } catch (\Exception $e) {
            $logger->error('Error resending OTP: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse([
                'success' => false,
                'message' => 'Error resending OTP'
            ], 500);
        }
    }



    // Account Verification Controllers for Mobile Emds


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/account/register/1718604634", name="Swami-account-register")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function SwamiregisterAction(
        Request $request,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger,
        SessionInterface $session
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(RegistrationFormType::class, $formData, ['hidePassword' => false]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $Phonetoken = bin2hex(random_bytes(32));
                        $customer->setPhoneVerificationToken(Text::toUrl($Phonetoken));
                        $Emailotp = random_int(100000, 999999);
                        $customer->setEmailVerificationOTP($Emailotp);
                        $Phoneotp = random_int(100000, 999999);
                        $customer->setMobileVerificationOTP($Phoneotp);
                        $customer->setRefferedBy('Swami Nathan');
                        $customer->save();

                        

                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken, $Emailotp);

                        $WelcometemplateID= "843ce716-d7f3-4e46-97c4-b8be3c130665";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $WelcometemplateID);

                        $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                        $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);


                        // $response = $this->redirectToRoute('Account-Verification-Step-1');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies

                        // $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login

                        // $authenticationLoginListener->doEcommerceFrameworkLogin($customer);
                        // $session->set('verification_needed', true);

                        $custuserID = $customer->getUserID();

                        return $this->redirectToRoute('Account-Verification-OTP', ['url' => $custuserID]);

                        
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        // Get reCAPTCHA configuration
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        
        return $this->render('account/register.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'recaptcha_site_key' => $recaptchaConfig['site_key']
        ]);
    }



    private function GupsendWhatsAppMessage($phoneNumber, $firstName, $templateID)
    {
        // Get Gupshup API credentials from environment
        $gupshupConfig = \App\Service\EnvironmentConfigService::getGupshupConfig();
        $apiUrl = $gupshupConfig['api_url'];
        $apiKey = $gupshupConfig['api_key'];
        $source = $gupshupConfig['source_number'];
        $srcName = $gupshupConfig['source_name'];
        $templateId = $templateID;
        
        if (empty($apiKey) || empty($source) || empty($srcName)) {
            throw new \Exception('Gupshup WhatsApp API credentials not configured');
        }
        
        // Prepare the template with params (e.g., $firstName)
        $templateData = json_encode([
            'id' => $templateId,
            'params' => [$firstName]
        ]);

        // Prepare the data for the POST request
        $data = http_build_query([
            'channel' => 'whatsapp',
            'source' => $source,
            'destination' => $phoneNumber,
            'src.name' => $srcName,
            'template' => $templateData
        ]);

        // Initialize cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            'apikey: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute the request
        $response = curl_exec($ch);

        // Handle errors if needed
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // You can log the error message or throw an exception
            throw new \Exception("Gupshup API request failed: " . $error_msg);
        }

        // Close the cURL session
        curl_close($ch);

        // Optionally log the response for debugging purposes
        // error_log('Gupshup API Response: ' . $response);
    }



    private function verifyRecaptcha($recaptchaToken) {
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        $secret = $recaptchaConfig['secret_key'];
        
        if (empty($secret)) {
            throw new \Exception('reCAPTCHA secret key not configured');
        }
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptchaToken");
        $result = json_decode($response, true);
    
        return $result;
    }


    private function sendWhatsAppOTPWithMeta($phoneNumber, $otp, LoggerInterface $logger)
    {
        try {
            $logger->info('Starting Meta WhatsApp OTP send process', [
                'phone_number' => $phoneNumber,
                'otp' => $otp
            ]);
            
            // Meta WhatsApp API Configuration from environment variables
            $metaConfig = \App\Service\EnvironmentConfigService::getMetaWhatsAppConfig();
            $accessToken = $metaConfig['access_token'];
            $phoneNumberId = $metaConfig['phone_number_id'];
            $apiVersion = 'v22.0';
            $templateName = 'arqonzotp';
            $languageCode = 'en';
            
            $logger->info('Meta WhatsApp API Configuration', [
                'phone_number_id' => $phoneNumberId,
                'api_version' => $apiVersion,
                'template_name' => $templateName,
                'language_code' => $languageCode
            ]);
            
            // First, let's verify the phone number ID and access token
            $this->verifyWhatsAppConfiguration($accessToken, $phoneNumberId, $apiVersion, $logger);
            
            // Construct the API URL
            $apiUrl = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";
            
            // Ensure phone number is in correct format (with country code, no + sign)
            $formattedPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
            
            // Add country code if not present (assuming India +91)
            if (strlen($formattedPhone) === 10) {
                $formattedPhone = '91' . $formattedPhone;
            }
            
            $logger->info('Phone number formatting', [
                'original_phone' => $phoneNumber,
                'formatted_phone' => $formattedPhone
            ]);
            
            // Prepare the message payload for AUTHENTICATION TEMPLATE
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => strval($otp)
                                ]
                            ]
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => '0',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => strval($otp)
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            
            // Convert payload to JSON
            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            
            $logger->info('Meta WhatsApp OTP Request prepared', [
                'api_url' => $apiUrl,
                'payload' => $jsonPayload,
                'formatted_phone' => $formattedPhone
            ]);
            
            // Initialize cURL
            $ch = curl_init($apiUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $logger->info('Sending Meta WhatsApp API request');
            
            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $logger->info('Meta WhatsApp API response received', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            // Handle cURL errors
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                $logger->error('Meta WhatsApp API cURL error', [
                    'error' => $error_msg
                ]);
                throw new \Exception("Meta WhatsApp API cURL error: " . $error_msg);
            }
            
            curl_close($ch);
            
            // Parse and validate response
            $responseData = json_decode($response, true);
            
            if ($httpCode !== 200) {
                $errorMessage = isset($responseData['error']['message']) 
                    ? $responseData['error']['message'] 
                    : 'Unknown error occurred';
                
                $errorDetails = isset($responseData['error']) 
                    ? json_encode($responseData['error']) 
                    : $response;
                
                $logger->error('Meta WhatsApp API error', [
                    'http_code' => $httpCode,
                    'error_message' => $errorMessage,
                    'error_details' => $errorDetails,
                    'response' => $response
                ]);
                    
                throw new \Exception("Meta WhatsApp API error (HTTP {$httpCode}): " . $errorMessage . " | Details: " . $errorDetails);
            }
            
            $logger->info('Meta WhatsApp OTP sent successfully', [
                'response_data' => $responseData
            ]);
            
            return $responseData;
            
        } catch (\Exception $e) {
            $logger->error('Meta WhatsApp OTP Error', [
                'error_message' => $e->getMessage(),
                'phone_number' => $phoneNumber,
                'otp' => $otp
            ]);
            throw $e;
        }
    }
    
    private function verifyWhatsAppConfiguration($accessToken, $phoneNumberId, $apiVersion, LoggerInterface $logger)
    {
        try {
            $logger->info('Starting WhatsApp configuration verification');
            
            // First, verify the access token by getting user info
            $userInfoUrl = "https://graph.facebook.com/{$apiVersion}/me?access_token={$accessToken}";
            
            $logger->info('Verifying access token', ['url' => $userInfoUrl]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $userResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $logger->info('Access token verification response', [
                'http_code' => $httpCode,
                'response' => $userResponse
            ]);
            
            if ($httpCode !== 200) {
                throw new \Exception("Failed to verify access token. HTTP Code: {$httpCode}");
            }
            
            $userData = json_decode($userResponse, true);
            
            if (isset($userData['error'])) {
                throw new \Exception("Invalid access token: " . $userData['error']['message']);
            }
            
            $logger->info('Access token verified successfully', [
                'user_name' => $userData['name'] ?? 'Unknown'
            ]);
            
            // Now verify the phone number ID
            $phoneInfoUrl = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}?access_token={$accessToken}";
            
            $logger->info('Verifying phone number ID', ['url' => $phoneInfoUrl]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $phoneInfoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $phoneResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $logger->info('Phone number ID verification response', [
                'http_code' => $httpCode,
                'response' => $phoneResponse
            ]);
            
            if ($httpCode !== 200) {
                throw new \Exception("Failed to verify phone number ID. HTTP Code: {$httpCode}. Response: " . $phoneResponse);
            }
            
            $phoneData = json_decode($phoneResponse, true);
            
            if (isset($phoneData['error'])) {
                throw new \Exception("Invalid phone number ID: " . $phoneData['error']['message']);
            }
            
            $logger->info('Phone number ID verified successfully', [
                'display_phone_number' => $phoneData['display_phone_number'] ?? 'Unknown'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            $logger->error('WhatsApp Configuration Verification Error', [
                'error_message' => $e->getMessage(),
                'phone_number_id' => $phoneNumberId,
                'api_version' => $apiVersion
            ]);
            // Don't throw exception here, just log and continue
            $logger->info('Skipping verification due to error, proceeding with OTP send...');
            return true;
        }
    }



    private function sendWhatsAppMessage($phoneNumber, $firstName, $templateID)
    {
        // Get Gupshup API credentials from environment
        $gupshupConfig = \App\Service\EnvironmentConfigService::getGupshupConfig();
        $apiUrl = $gupshupConfig['api_url'];
        $apiKey = $gupshupConfig['api_key'];
        $source = $gupshupConfig['source_number'];
        $srcName = $gupshupConfig['source_name'];
        $templateId = $templateID;
        
        if (empty($apiKey) || empty($source) || empty($srcName)) {
            throw new \Exception('Gupshup WhatsApp API credentials not configured');
        }
        
        // Prepare the template with params (e.g., $firstName)
        $templateData = json_encode([
            'id' => $templateId,
            'params' => [$firstName]
        ]);

        // Prepare the data for the POST request
        $data = http_build_query([
            'channel' => 'whatsapp',
            'source' => $source,
            'destination' => $phoneNumber,
            'src.name' => $srcName,
            'template' => $templateData
        ]);

        // Initialize cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            'apikey: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute the request
        $response = curl_exec($ch);

        // Handle errors if needed
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // You can log the error message or throw an exception
            throw new \Exception("Gupshup API request failed: " . $error_msg);
        }

        // Close the cURL session
        curl_close($ch);

        // Optionally log the response for debugging purposes
        // error_log('Gupshup API Response: ' . $response);
    }

    private function sendEventWhatsAppMessage($phoneNumber, $firstName, $admitId, $templateID)
    {
        // Get Gupshup API credentials from environment
        $gupshupConfig = \App\Service\EnvironmentConfigService::getGupshupConfig();
        $apiUrl = $gupshupConfig['api_url'];
        $apiKey = $gupshupConfig['api_key'];
        $source = $gupshupConfig['source_number'];
        $srcName = $gupshupConfig['source_name'];
        $templateId = $templateID;
        
        if (empty($apiKey) || empty($source) || empty($srcName)) {
            throw new \Exception('Gupshup WhatsApp API credentials not configured');
        }
        
        // Prepare the template with params (e.g., $firstName)
        $templateData = json_encode([
            'id' => $templateId,
            'params' => [$firstName, $admitId]
        ]);

        // Prepare the data for the POST request
        $data = http_build_query([
            'channel' => 'whatsapp',
            'source' => $source,
            'destination' => $phoneNumber,
            'src.name' => $srcName,
            'template' => $templateData
        ]);

        // Initialize cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            'apikey: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute the request
        $response = curl_exec($ch);

        // Handle errors if needed
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // You can log the error message or throw an exception
            throw new \Exception("Gupshup API request failed: " . $error_msg);
        }

        // Close the cURL session
        curl_close($ch);

        // Optionally log the response for debugging purposes
        // error_log('Gupshup API Response: ' . $response);
    }


    private function sendEmailVerification($customer, $EmailTokenURL, $Emailotp)
    {
        // Fetch the email template
        $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
        $EmailTemplates->addConditionParam("TemplateName = ?", "EmailVerificationEmail");
        $EmailTemplate = $EmailTemplates->load();
        $EmailTemplate = $EmailTemplate[0];

        // Get subject and HTML content
        $subject = $EmailTemplate->getSubject();
        $htmlContent = $EmailTemplate->getContent();

        // Replace placeholders with actual values
        $htmlContent = str_replace("{EmailTokenURL}", $EmailTokenURL, $htmlContent);
        $htmlContent = str_replace("{OTP}", $Emailotp, $htmlContent);
        $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);

        // Create a new Pimcore\Mail instance
        $mail = new \Pimcore\Mail();
        $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
        $mail->to($customer->getEmail());
        $mail->subject($subject);
        $mail->html($htmlContent);
        $mail->send();
    }



    
    /**
     * Send a welcome email to the user.
     *
     * @param CustomerInterface $customer
     */
    private function sendWelcomeEmail(CustomerInterface $customer): void
    {   
        $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
        $EmailTemplates->addConditionParam("TemplateName = ?", "WelcomeEmail");
        $EmailTemplate = $EmailTemplates->load();
        $EmailTemplate = $EmailTemplate[0];

        $subject = $EmailTemplate->getSubject();
        $htmlContent = $EmailTemplate->getContent();
        eval("\$htmlContent = \"$htmlContent\";");
        // Create a new Pimcore\Mail instance
        $mail = new \Pimcore\Mail();
        // $mail->from('arqonztest@gmail.com');
        $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
        $mail->to($customer->getEmail());
        $mail->subject($subject);
        $mail->html($htmlContent);
        $mail->send();

    }

    /**
     * @Route("/account/update-marketing", name="account-update-marketing-permission")
     *
     * @param Request $request
     * @param Service $consentService
     * @param Translator $translator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    #[IsGranted('ROLE_USER')]
    public function updateMarketingPermissionAction(Request $request, Service $consentService, Translator $translator, NewsletterDoubleOptInService $newsletterDoubleOptInService, UserInterface $user = null)
    {
        if ($user instanceof Customer) {
            $currentNewsletterPermission = $user->getNewsletter()->getConsent();
            if (!$currentNewsletterPermission && $request->get('newsletter')) {
                $consentService->giveConsent($user, 'newsletter', $translator->trans('general.newsletter'));
                $newsletterDoubleOptInService->sendDoubleOptInMail($user, $this->document->getProperty('newsletter_confirm_mail'));
            } elseif ($currentNewsletterPermission && !$request->get('newsletter')) {
                $user->setNewsletterConfirmed(false);
                $consentService->revokeConsent($user, 'newsletter');
            }

            $currentProfilingPermission = $user->getProfiling()->getConsent();
            if (!$currentProfilingPermission && $request->get('profiling')) {
                $consentService->giveConsent($user, 'profiling', $translator->trans('general.profiling'));
            } elseif ($currentProfilingPermission && !$request->get('profiling')) {
                $consentService->revokeConsent($user, 'profiling');
            }

            $user->save();

            $this->addFlash('success', $translator->trans('account.marketing-permissions-updated'));
        }

        return $this->redirectToRoute('account-index');
    }

    /**
     * @Route("/account/confirm-newsletter", name="account-confirm-newsletter")
     *
     * @param Request $request
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param Translator $translator
     *
     * @return RedirectResponse
     */
    public function confirmNewsletterAction(Request $request, NewsletterDoubleOptInService $newsletterDoubleOptInService, Translator $translator)
    {
        $token = $request->get('token');
        $customer = $newsletterDoubleOptInService->handleDoubleOptInConfirmation($token);
        if ($customer) {
            $this->addFlash('success', $translator->trans('account.marketing-permissions-confirmed-newsletter'));

            return $this->redirectToRoute('account-index');
        } else {
            throw new NotFoundHttpException('Invalid token');
        }
    }

    /**
     * @Route("/account/send-password-recovery", name="account-password-send-recovery")
     *
     * @param Request $request
     * @param PasswordRecoveryService $service
     * @param Translator $translator
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function sendPasswordRecoveryMailAction(Request $request, PasswordRecoveryService $service, Translator $translator)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            try {
                $service->sendRecoveryMail(
                    $request->get('email', ''),
                    $this->document->getProperty('password_reset_mail')
                );

                $this->addFlash('success', $translator->trans('account.reset-mail-sent-when-possible'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('account-login', ['no-referer-redirect' => true]);
        }

        return $this->render('account/send_password_recovery_mail.html.twig', [
            'hideBreadcrumbs' => true,
            'emailPrefill' => $request->get('email')
        ]);
    }

    /**
     * @Route("/account/reset-password", name="account-reset-password")
     *
     * @param Request $request
     * @param PasswordRecoveryService $service
     * @param Translator $translator
     *
     * @return Response|RedirectResponse
     */
    public function resetPasswordAction(Request $request, PasswordRecoveryService $service, Translator $translator)
    {
        $token = $request->get('token');
        $customer = $service->getCustomerByToken($token);
        $error = null;
        try {
            if (!$customer) {
                throw new NotFoundHttpException('Invalid token');
            }

            if ($request->isMethod(Request::METHOD_POST)) {

                $newPassword = $request->get('password');

                $this->checkPassword($newPassword);

                $service->setPassword($token, $newPassword);

                $this->addFlash('success', $translator->trans('account.password-reset-successful'));

                return $this->redirectToRoute('account-login', ['no-referer-redirect' => true]);

            }

        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return $this->render('account/reset_password.html.twig', [
            'hideBreadcrumbs' => true,
            'token' => $token,
            'email' => $customer?->getEmail(),
            'error' => $error
        ]);
    }


    /**
     *
     *
     * @Route("/account/notifications", name="account-notifications")
     *
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function NotificationsAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            // $PortfolioActivate = $customer->getPortfolioActivate();
            // $ProProfile = $customer->getPortfolio();
            $NotificationList = $customer->getNotifications();
            $NotificationList = array_reverse($NotificationList);
            
            return $this->render('Professional/Notifications.html.twig', [
                'notifications' => $NotificationList,
                'customertype' =>$customertype.'s',
            ]);
            
        }
    }

    /**
     * @Route("/mark-notification-as-read/{id}", name="mark_notification_as_read")
     */
    public function markNotificationAsReadAction($id): JsonResponse
    {   
        $notification = ProNotification::getByPath("/Services/Notifications/$id");
        $notification->setReadStatus('read'); // Assuming your Notification entity has a method to set the read status
        $notification->save();
        return new JsonResponse(['success' => true]);
    }


    /**
     *
     * This could be further separated into services, but was kept as single method for demonstration purposes as the
     * registration process is different on every project.
     *
     * @Route("/vit/register", name="Event-account-register")
     *
     * @param Request $request
     * @param CustomerProviderInterface $customerProvider
     * @param LoginManagerInterface $loginManager
     * @param RegistrationFormHandler $registrationFormHandler
     * @param AuthenticationLoginListener $authenticationLoginListener
     * @param Translator $translator
     * @param Service $consentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NewsletterDoubleOptInService $newsletterDoubleOptInService
     * @param UserInterface|null $user
     *
     * @return Response|RedirectResponse
     */
    public function EventregisterAction(
        Request $request,
        CustomerProviderInterface $customerProvider,
        LoginManagerInterface $loginManager,
        RegistrationFormHandler $registrationFormHandler,
        AuthenticationLoginListener $authenticationLoginListener,
        Translator $translator,
        Service $consentService,
        UrlGeneratorInterface $urlGenerator,
        NewsletterDoubleOptInService $newsletterDoubleOptInService,
        UserInterface $user = null, LoggerInterface $logger
    ) {

        //redirect user to index page if logged in
        if ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('account-index');
        }


        // create a new, empty customer instance
        /** @var CustomerInterface|\Pimcore\Model\DataObject\Customer $customer */
        $customer = $customerProvider->create();

        // the registration form handler is just a utility class to map pimcore object data to form
        // and vice versa.
        $formData = $registrationFormHandler->buildFormData($customer);

        // build the registration form and pre-fill it with customer data
        $form = $this->createForm(EventRegistrationFormType::class, $formData, ['hidePassword' => false]);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // Capture the plaintext password before encoding and saving

            $recaptchaToken = $form->get('recaptchaToken')->getData(); // Fetch the reCAPTCHA token
            $logger->info("reCAPTCHA Token from Request: " . $recaptchaToken);

            $designation = $form->get('designation')->getData();
            $company = $form->get('Company')->getData();
            $City = $form->get('City')->getData();
            $imageData = $request->files->get('PassportImage');

            try {
                $response = $this->verifyRecaptcha($recaptchaToken);

                $logger->info('reCAPTCHA Response: ', $response);

    
                // Check if the reCAPTCHA is valid
                if ($response['success'] || $response['score'] > 0.5) {
                    $logger->info("reCAPTCHA Verification Success. ");
                    $registrationFormHandler->updateCustomerFromForm($customer, $form);
                    $customer->setCustomerLanguage($request->getLocale());
                    $customer->setEventPass("VIT");
                    $customer->setDesignation($designation);
                    $customer->setcompany($company);
                    $customer->setcity($City);

                    

                    if ($imageData) {
                        $previousimage = $customer->getProfilePicture();
                        if ($previousimage) {
                            $previousimage->delete();
                        }
    
                        $imageName = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $imageData->getClientOriginalExtension();
                        $newAsset = new \Pimcore\Model\Asset\Image();
                        $newAsset->setFilename($imageName);
    
                        $folderPath = "/ProfilePictures/";
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath($folderPath));
    
                        $newAsset->setData(file_get_contents($imageData->getPathname()));
                        $newAsset->save();
                        $customer->setProfileImage($newAsset);
                    }
                    
                    $customer->setActive(true);

                    try {
                        $this->checkPassword($form->getData()['password']);
                        $timestamp = time();
                        $randomDigit = rand(0, 9);
                        $userID = $timestamp . $randomDigit;
                        $customer->setUserID(Text::toUrl($userID));
                        $customer->setPS($form->getData()['password']);
                        $Emailtoken = bin2hex(random_bytes(32));
                        $customer->setEmailVerificationToken(Text::toUrl($Emailtoken));
                        $customer->save();



                        if ($form->getData()['newsletter']) {
                            $consentService->giveConsent($customer, 'newsletter', $translator->trans('general.newsletter'));
                            $newsletterDoubleOptInService->sendDoubleOptInMail($customer, $this->document->getProperty('newsletter_confirm_mail'));
                        }
                        if ($form->getData()['profiling']) {
                            $consentService->giveConsent($customer, 'profiling', $translator->trans('general.profiling'));
                        }

                        $Notification = new ProNotification();
                        $Notification->setParent(DataObjectService::createFolderByPath('/Services/Notifications'));
                        $Notification->setKey(Text::toUrl(time()));
                        $Notification->setMessage("Welcome to Arqonz! Submit and Complete your Profile details now.");
                        $Notification->setDescription("Click to Open Submit Details.");
                        $Notification->setCustomer($customer);
                        $redirecturl = '/account/Profile';

                        $Notification->seturl($redirecturl);
                        $Notification->setPublished(true);
                        $Notification->save();
                        $plainPassword = $form->get('password')->getData();
                        $this->sendEventWelcomeEmail($customer);
                        $this->sendEmailVerification($customer, $Emailtoken);


                        $adminId="vit/download-admin-card/".$customer->getUserID();
                        $WelcometemplateID= "b7d1f69e-9ab2-431b-b5ae-dfbc31d3b0cb";
                        $this->sendEventWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $adminId, $WelcometemplateID);

                        $ZemchtemplateID = "b38afabc-a26c-4e86-8601-e2ab20113d2f";
                        $this->sendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $ZemchtemplateID);

                        $response = $this->redirectToRoute('account-index');

                        // log user in manually
                        // pass response to login manager as it adds potential remember me cookies
                        $loginManager->login($customer, $request, $response);

                        //do ecommerce framework login
                        $authenticationLoginListener->doEcommerceFrameworkLogin($customer);

                        return $response;
                    } catch (DuplicateCustomerException $e) {
                        $errors[] = $translator->trans(
                            'account.customer-already-exists',
                            [
                                $customer->getEmail(),
                                $urlGenerator->generate('account-password-send-recovery', ['email' => $customer->getEmail()])
                            ]
                        );
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                } else {
                    // Recaptcha verification failed
                    $logger->error('reCAPTCHA verification failed: ', $response);
                    $errors[] = $translator->trans('account.recaptcha-failed', [], 'messages');
                }
            } catch (\Exception $e) {
                // Handle recaptcha verification failure (exception case)
                $logger->error('reCAPTCHA verification exception: ' . $e->getMessage());
                $errors[] = $translator->trans('account.recaptcha-exception', [], 'messages');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        // Get reCAPTCHA configuration
        $recaptchaConfig = \App\Service\EnvironmentConfigService::getRecaptchaConfig();
        
        return $this->render('account/register.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'errors' => $errors,
            'hideBreadcrumbs' => true,
            'hidePassword' => false,
            'recaptcha_site_key' => $recaptchaConfig['site_key']
        ]);
    }



    /**
     * Send a welcome email to the user.
     *
     * @param CustomerInterface $customer
     */
    private function sendEventWelcomeEmail(CustomerInterface $customer): void
    {   
        $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
        $EmailTemplates->addConditionParam("TemplateName = ?", "EventWelcomeEmail");
        $EmailTemplate = $EmailTemplates->load();
        $EmailTemplate = $EmailTemplate[0];


        $subject = $EmailTemplate->getSubject();
        $htmlContent = $EmailTemplate->getContent();
        $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
        $htmlContent = str_replace("{AdmitcardURL}", "https://arqonz.com/vit/download-admin-card/".$customer->getUserID(), $htmlContent);
        $htmlContent = str_replace("{CustomerEmail}", $customer->getemail(), $htmlContent);
        $htmlContent = str_replace("{CustomerPS}", $customer->getPS(), $htmlContent);
        // Create a new Pimcore\Mail instance
        $mail = new \Pimcore\Mail();
        // $mail->from('arqonztest@gmail.com');
        $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
        $mail->to($customer->getEmail());
        $mail->subject($subject);
        $mail->html($htmlContent);
        $mail->send();

    }


}