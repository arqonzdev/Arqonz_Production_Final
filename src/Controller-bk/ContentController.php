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


use App\Form\CarSubmitFormType;
use Pimcore\Model\DataObject\ArchitectProfile;
use Pimcore\Model\DataObject\ProProfile;
use Pimcore\Model\DataObject\BuilderProfile;
use Pimcore\Model\DataObject\BuilderProjects;
use Pimcore\Model\DataObject\ArchitectProjects;
use Pimcore\Model\DataObject\ProProject;
use Pimcore\Model\DataObject\ArchitectEnquiry;
use Pimcore\Model\DataObject\BuilderEnquiry;
use Pimcore\Model\DataObject\ProEnquiry;
use Pimcore\Model\DataObject\ProNotification;
use Pimcore\Model\DataObject\Customer;
use Pimcore\Model\DataObject\ProRequirementProduct;
use Pimcore\Model\DataObject\ProProposalBid;
use Pimcore\Model\DataObject\SupplierBid;
use Pimcore\Model\DataObject\PaymentOrder;
//use App\Model\Customer;
use Pimcore\Model\DataObject\ProRequirement;
use Pimcore\Model\DataObject\ManufacturerRefferal;
use Pimcore\Model\DataObject\SupplierPinnedNotification;
use Pimcore\Model\DataObject\ProProposal;
use Pimcore\Model\DataObject\ProProduct;
use Pimcore\Model\DataObject\NewsLetter;
use Pimcore\Model\DataObject\ProEndorsement;
use Pimcore\Model\DataObject\ProEndorsementRequest;
use App\Form\ArchitectRegistrationFormType;
use App\Form\ContractorRegistrationFormType;
use App\Form\DesignerRegistrationFormType;
use App\Form\ArchitectAddProjectFormType;
use App\Form\BuilderAddProjectFormType;
use App\Form\ContractorAddProjectFormType;
use App\Form\DesignerAddProjectFormType;
use App\Form\ArchitectEnquiryFormType;
use App\Form\BuilderEnquiryFormType;
use App\Form\BuilderRegistrationFormType;
use App\Form\ProRequirementFormType;
use App\Form\ProEnquiryFormType;
use App\Form\Profile_Edit_FormType;
use App\Form\EngineerRegistrationFormType;
use App\Form\RetailerRegistrationFormType;
use App\Form\FilterFormType;
use App\Form\BuilderProfileUpdateFormType;
use App\Form\ArchitectEditProjectFormType;
use App\Form\DesignerEditProjectFormType;
use App\Form\ContractorEditProjectFormType;
use App\Form\BuilderEditProjectFormType;
use App\Form\ProRequirementEditFormType;
use App\Form\ManufacturerRefferalFormtype;
use App\Form\ProProposalFormType;
use App\Form\ManufacturerRegistrationFormType;
use App\Form\DistributorRegistrationFormType;
use App\Form\ProEndorsementFormType;
use App\Form\AddProductFormType;
use App\Form\SearchFormType;
use App\Form\NewsLetterFormType;
use App\Form\EndorsementRequestFormType;
use App\Form\EndorsementRequestFormTypeTEST;
use App\Form\EngineerAddProjectFormType;
use App\Form\EndorsementType;
use App\Form\AddProductFormTESTtype;
use App\Model\Product\Car;
use App\Website\Tool\Text;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Controller\Attribute\ResponseHeader;
use Pimcore\Model\DataObject\BodyStyle;
use Pimcore\Model\DataObject\Manufacturer;
use Pimcore\Model\DataObject\Service;
use Pimcore\Translation\Translator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Document;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Services\PasswordRecoveryService;
use CustomerManagementFrameworkBundle\CustomerProvider\CustomerProviderInterface;
use CustomerManagementFrameworkBundle\Model\CustomerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ProProfile\Listing;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Pimcore\Mail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Transport;
use Razorpay\Api\Api;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Pimcore\Model\Asset;
use Carbon\Carbon;
use Pimcore\Db;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
// TCPDF
use TCPDF;
// DOMPDF
use Dompdf\Dompdf;
use Dompdf\Options;
use DateTimeZone;

use Symfony\Component\HttpFoundation\JsonResponse;

class ContentController extends BaseController
{
    #[Template('content/default.html.twig')]
    public function defaultAction()
    {
        return [];
    }

    /**
     * The attribute below demonstrate the ResponseHeader attribute which can be
     * used to set custom response headers on the auto-rendered response. At this point, the headers
     * are not really set as we don't have a response yet, but they will be added to the final response
     * by the ResponseHeaderListener.
     *
     *
     * @return Response
     */
    #[ResponseHeader(key: "X-Custom-Header", values: ["Foo", "Bar"])]
    #[ResponseHeader(key: "X-Custom-Header", values: ["Bazinga"], replace: true)]
    public function portalAction(Request $request, Translator $translator)
    {   
        $ArchitectProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ArchitectProfilesList->addConditionParam("PortfolioType = ?", "Architect");
        $Architects = $ArchitectProfilesList->load();
        $Architects = array_slice($Architects, 0, 2);

        $DesignerProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $DesignerProfilesList->addConditionParam("PortfolioType = ?", "Designer");
        $Designers = $DesignerProfilesList->load();
        $Designers = array_slice($Designers, 0, 2);

        $BuilderProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $BuilderProjects = $BuilderProjectsList->load();

        $filteredProjects = array_filter($BuilderProjects, function ($project) {
            // Adjust these conditions based on your requirements
            $ProfessionalPath = strtolower($project->getProfessionalPath());
    
            // "OR" condition: If the keyword is present in any of the fields, include the profile
            return strpos($ProfessionalPath, 'builder') !== false;
        });
        $BuilderProjects = $filteredProjects;

        


        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $searchKeyword = $form->get('Search')->getData();
            return $this->redirect("Search/$searchKeyword");
        }

        $form1 = $this->createForm(NewsLetterFormType::class);
        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
            $formData1 = $form1->getData();
            
            $NewsLetter = new NewsLetter();
            $NewsLetter->setKey(Text::toUrl(time()));
            $NewsLetter->setParent(Service::createFolderByPath('/NewsLetter'));
            $NewsLetter->setFullName($form1->get('FullName')->getData());
            $NewsLetter->setEmail($form1->get('Email')->getData());
            
            $NewsLetter->setPublished(true);

            $NewsLetter->save();
            $this->addFlash('success', 'You are added to our NewsLetter.');
        }

        // you can also set the header via code 
        $this->addResponseHeader('X-Custom-Header3', ['foo', 'bar']);

        return $this->render('Professional/home-demo-new.html.twig', [
            'isPortal' => true,
            'form' => $form->createView(),
            'form1' => $form1->createView(), 
            'architects' => $Architects,
            'designers' => $Designers,
            'projects' => $BuilderProjects,

        ]);
    }


    /**
     * @Route("/home-demo", name="HomeDemo")
     */
    public function HomeDemo( Request $request, PaginatorInterface $paginator)
    {
        $ArchitectProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ArchitectProfilesList->addConditionParam("PortfolioType = ?", "Architect");
        $Architects = $ArchitectProfilesList->load();
        $Architects = array_slice($Architects, 0, 2);

        $DesignerProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $DesignerProfilesList->addConditionParam("PortfolioType = ?", "Designer");
        $Designers = $DesignerProfilesList->load();
        $Designers = array_slice($Designers, 0, 2);

        $ContractorProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ContractorProfilesList->addConditionParam("PortfolioType = ?", "Contrctor");
        $Contractors = $ContractorProfilesList->load();
        $Contractors = array_slice($Contractors, 0, 2);

        $BuilderProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $BuilderProjects = $BuilderProjectsList->load();

        $filteredProjects = array_filter($BuilderProjects, function ($project) {
            // Adjust these conditions based on your requirements
            $ProfessionalPath = strtolower($project->getProfessionalPath());
    
            // "OR" condition: If the keyword is present in any of the fields, include the profile
            return strpos($ProfessionalPath, 'builder') !== false;
        });
        $BuilderProjects = $filteredProjects;

        


        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $searchKeyword = $form->get('Search')->getData();
            return $this->redirect("Search/$searchKeyword");
        }

        $form1 = $this->createForm(NewsLetterFormType::class);
        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
            $formData1 = $form1->getData();
            
            $NewsLetter = new NewsLetter();
            $NewsLetter->setKey(Text::toUrl(time()));
            $NewsLetter->setParent(Service::createFolderByPath('/NewsLetter'));
            $NewsLetter->setFullName($form1->get('FullName')->getData());
            $NewsLetter->setEmail($form1->get('Email')->getData());
            
            $NewsLetter->setPublished(true);

            $NewsLetter->save();
            $this->addFlash('success', 'You are added to our NewsLetter.');
        }

        // you can also set the header via code 
        $this->addResponseHeader('X-Custom-Header3', ['foo', 'bar']);

        return $this->render('Professional/home-demo-new.html.twig', [
            'isPortal' => true,
            'form' => $form->createView(),
            'form1' => $form1->createView(), 
            'architects' => $Architects,
            'designers' => $Designers,
            'projects' => $BuilderProjects,

        ]);
    }

    /**
     * @Route("/products", name="Products-List")
     */
    public function ProductsListAction( Request $request, PaginatorInterface $paginator, LoggerInterface $logger)
    {
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT ID, Product_Name FROM products";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Log the entire products array
        $logger->info('Products Array:', ['products' => $products]);

        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $logger->info("Product Price: " . $product['Product_Price']);
        }

        // Pagination
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template
        return $this->render('Professional/products_list.html.twig', [
            'products' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/Search/{url}", name="Search_page")
     */
    public function SearchAction($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Fetch Profiles objects
        $keyword = $url;
        $proProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        // $proProfileList->addConditionParam("LOWER(PortfolioType) REGEXP LOWER(?)", $url);
        //$proProfileList->addConditionParam("CompanyName LIKE?", "$url");
        // $proProfileList->addConditionParam("Description LIKE ?", "$url");
        

        $ProProfiles = $proProfileList->load();

        $filteredProfiles = array_filter($ProProfiles, function ($profile) use ($keyword) {
            // Adjust these conditions based on your requirements
            $portfolioType = strtolower($profile->getPortfolioType());
            $companyName = strtolower($profile->getCompanyName());
            $description = strtolower($profile->getDescription());
    
            // "OR" condition: If the keyword is present in any of the fields, include the profile
            return strpos($portfolioType, $keyword) !== false ||
                   strpos($companyName, $keyword) !== false ||
                   strpos($description, $keyword) !== false;
        });
        $ProProfiles = $filteredProfiles;

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();


        // Render the template with the architect profiles
        return $this->render('Professional/Searchlisting.html.twig', [
            'ProProfiles' => $pagination,
            'paginationVariables' => $paginationVariables,
            'Keyword' => $keyword,
        ]);
    }

    /**
     * @return Response
     */
    public function editableRoundupAction()
    {
        return $this->render('content/editable_roundup.html.twig');
    }

    /**
     * @return Response
     */
    public function thumbnailsAction()
    {
        return $this->render('content/thumbnails.html.twig');
    }

    /**
     * @param Request $request
     * @param Translator $translator
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function carSubmitAction(Request $request, Translator $translator)
    {
        $form = $this->createForm(CarSubmitFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $car = new Car();
            $car->setParent(Service::createFolderByPath('/upload/new'));
            $car->setKey(Text::toUrl($formData['name'] . '-' . time()));

            $car->setName($formData['name']);
            $car->setDescription($formData['description']);
            $car->setManufacturer(Manufacturer::getById($formData['manufacturer']));
            $car->setBodyStyle(BodyStyle::getById($formData['bodyStyle']));
            $car->setCarClass($formData['carClass']);

            $car->save();

            $this->addFlash('success', $translator->trans('general.car-submitted'));

            return $this->render('content/car_submit_success.html.twig', ['car' => $car]);
        }

        return $this->render('content/car_submit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Factory $ecommerceFactory
     *
     * @return Response
     */
    public function tenantSwitchesAction(Request $request, Factory $ecommerceFactory)
    {
        $environment = $ecommerceFactory->getEnvironment();

        if ($request->get('change-checkout-tenant')) {
            $checkoutTenant = $request->get('change-checkout-tenant');
            $checkoutTenant = $checkoutTenant == 'default' ? '' : $checkoutTenant;
            $environment->setCurrentCheckoutTenant(strip_tags($checkoutTenant));
            $environment->save();
        }

        if ($request->get('change-assortment-tenant')) {
            $assortmentTenant = $request->get('change-assortment-tenant');
            $assortmentTenant = $assortmentTenant == 'default' ? '' : $assortmentTenant;
            $environment->setCurrentAssortmentTenant(strip_tags($assortmentTenant));
            $environment->save();
        }

        $paramsBag['checkoutTenants'] = ['default' => ''];
        $paramsBag['currentCheckoutTenant'] = $environment->getCurrentCheckoutTenant() ? $environment->getCurrentCheckoutTenant() : 'default';

        $paramsBag['assortmentTenants'] = ['default' => '', 'ElasticSearch' => 'needs to be configured and activated in configuration'];
        $paramsBag['currentAssortmentTenant'] = $environment->getCurrentAssortmentTenant() ? $environment->getCurrentAssortmentTenant() : 'default';

        return $this->render('content/tenant_switches.html.twig', $paramsBag);
    }    
    

    /**
     * Index page for account - it is restricted to ROLE_USER via security annotation
     *
     * @Route("/account/index", name="account-index")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function architectDashboardAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $architectProfile = null;
            $architectActivate = null;

            $form1 = $this->createForm(SearchFormType::class);
            $form1->handleRequest($request);
            if ($form1->isSubmitted() && $form->isValid()) {
                $formData1 = $form1->getData();
                $searchKeyword = $form1->get('Search')->getData();
                return $this->redirect("Search/$searchKeyword");
            }
    
            
            if ($customertype === 'Customer'){

                return $this->render('account/customer_dashboard.html.twig', [
                    'customer' => $customer,
                ]);
            }
            elseif ($customertype === 'Contractor' || $customertype === 'Engineer' || $customertype === 'Designer' || $customertype === 'Architect' || $customertype === 'Builder' || $customertype === 'Manufacturer' || $customertype === 'Distributor' || $customertype === 'Retailer'){
                
                $PortfolioActivate = $customer->getPortfolioActivate();
                if($PortfolioActivate === 'true'){
                    $ProProfiles = $customer->getPortfolio();
                    $ProProfile = $ProProfiles[0];
                    

                    if($ProProfile){
                    
                        $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
                        $NotificationList->addConditionParam("professional = ?", $ProProfile);
            
                        $NotificationList->setOrderKey('creationDate');
                        $NotificationList->setOrder('desc');
        
                        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
                        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProProjectsList->setOrderKey('creationDate');
                        $ProProjectsList->setOrder('desc');

                        $ProProjects = $ProProjectsList->load();

                        $ProProducts = new \Pimcore\Model\DataObject\ProProduct\Listing();
                        $ProProducts->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProProducts->setOrderKey('creationDate');
                        $ProProducts->setOrder('desc');

                        $ProProducts = $ProProducts->load();

                        $form = $this->createForm(ProRequirementFormType::class);
                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
                            // Handle file upload and save the ProRequirement object

                            $uploadedFile = $form->get('excelFile')->getData();
                            $proRequirement = new ProRequirement();

                
                            try {
                                $asset = new Document();
                                $asset->setData(file_get_contents($uploadedFile->getPathname()));
                                $timestamp = time();
                                $originalFilename = $uploadedFile->getClientOriginalName();
                                $newFilename = $timestamp . '_' . $originalFilename;
                                $asset->setFilename($newFilename); // Set the desired filename

                                // Save the asset in the "/Services/Requirements" directory
                                if ($customertype === 'Contractor'){
                                    $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/Requirements"));
                                    }
                                elseif ($customertype === 'Designer'){
                                    $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/Requirements"));
                                    }
                                elseif ($customertype === 'Architect'){
                                    $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/Requirements"));
                                    }
                                elseif ($customertype === 'Builder'){
                                    $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/Requirements"));
                                    }
                                
                                $asset->save();
                                $proRequirement->setExcelFile($asset);
                                $proRequirement->setKey(time());
                                $proRequirement->setParent(Service::createFolderByPath('/Requirements'));
                                $proRequirement->setTitle($form->get('Title')->getData());
                                
                                $proRequirement->setDescription($form->get('Description')->getData());
                                $proRequirement->setProfessional($ProProfile);
                                $proRequirement->setProfessionalPath($ProProfile);
                                $excelData = $this->processExcelData($uploadedFile);
                                $proRequirement->setExcelData($excelData);
                                $proRequirement->setPublished(true);
                
                                $proRequirement->save();
            
                                // Redirect or do other actions
                                $this->addFlash('success', $translator->trans('Requirements submitted succesfully.'));
                            } catch (FileException $e) {
                                // Handle file upload error
                                // Log the error or show a flash message to the user
                            }
                        }

                        $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
                        $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProRequirementsList->setOrderKey('creationDate');
                        $ProRequirementsList->setOrder('desc');

                        $ProRequirements = $ProRequirementsList->load();
                        usort($ProRequirements, function($a, $b) {
                            return $b->getCreationDate() <=> $a->getCreationDate();
                        });
                        
                        // Load ProEnquiries

                        $ProEnquiryList = new \Pimcore\Model\DataObject\ProEnquiry\Listing();
                        $ProEnquiryList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProEnquiryList->setOrderKey('creationDate');
                        $ProEnquiryList->setOrder('desc');

                        $ProEnquiries = $ProEnquiryList->load();


                        $ProEndorsementList = new \Pimcore\Model\DataObject\ProEndorsement\Listing();
                        $ProEndorsementList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProEndorsementList->setOrderKey('creationDate');
                        $ProEndorsementList->setOrder('desc');

                        $ProEndorsements = $ProEndorsementList->load();

                    }

                    
                    return $this->render('Professional/Dashboard/dashboard.html.twig', [
                        'ProProfile' => $ProProfile,
                        'ProProjects' => $ProProjects,
                        'ProProducts' => $ProProducts,
                        'customer' => $customer,
                        'form' => $form->createView(), 
                        'form1' => $form1->createView(),
                        'Requirements' => $ProRequirements,
                        'ProEnquiries' => $ProEnquiries,
                        'ProEndorsements' => $ProEndorsements,                      
                    ]);
                }
                else{
                    return $this->render('account/dashboard.html.twig', [
                        'customer' => $customer,
                    ]);
                }
            }
        }
    
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    
    /**
     * @Route("/generate-quote", name="generate_quote", methods={"POST"})
     */
    public function generateQuote(Request $request, LoggerInterface $logger): Response
    {
        $key = $request->request->get('id');
        $logger->info('Received request for generate quote', ['id' => $key]);

        // Fetch ProRequirement based on the key
        $ProRequirementsLists = new \Pimcore\Model\DataObject\ProRequirement\Listing();
        $ProRequirementsLists->addConditionParam("ObjeKey = ?", $key); 
        $ProRequirements = $ProRequirementsLists->load();

        $selectedRequirement = $ProRequirements[0];

        if (!$selectedRequirement) {
            $logger->error('Requirement not found', ['id' => $key]);
            return new Response('Requirement not found', Response::HTTP_NOT_FOUND);
        }

        $products = $selectedRequirement->getProRequirementProduct();

        // Set up DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Set the default paper size to A4 with no margins
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'portrait');
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        // Prepare HTML content with watermark
        
        $html = '
            <html>
            <head>
                <style>
                    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");
                    body {
                        font-family: "Poppins", sans-serif;
                        margin: 0;
                        padding: 0;
                        width: 100%;
                    }
                    .header-section, .addresssection {
                        width: 100%;
                        margin-bottom: 20px;
                    }
                    .header-section table, .addresssection table, .tablesection table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .header-section table td, .addresssection table td, .tablesection table th, .tablesection table td {
                        padding: 8px;
                        
                    }
                    .header-section table td img {
                        width: 200px;
                    }
                    .billedby, .billedto {
                        width: 45%;
                        padding: 0px 10px;
                        align-content: baseline;
                        
                    }
                    .tablesection thead {
                        background-color: #Bbf1d6;
                    }
                    .tablesection tbody {
                        
                        text-align: center;
                    }
                    .tablesection {
                        border-radius: 13px;
                        overflow: hidden;
                    }
                    .totalsection {
                        text-align: right;
                        padding: 20px;
                    }
                    .main h1 {
                        margin: 10px 0px;
                    }
                    .main h2 {
                        margin: 10px 0px;
                    }
                    .fromaddress {
                        
                        padding: 5px 10px;
                        border-radius: 5px;
                    }
                    .toaddress {
                        
                        padding: 5px 10px;
                        border-radius: 5px;
                    }
                    
                    .billedby {
                        padding-right: 10px;
                    }
                    .billedto {
                        padding-left: 10px;
                    }
                    
                </style>
            </head>
            <body>
                <div class="main">
                    <div class="header-section">
                        <table>
                            <tr>
                                <td>
                                    <h1>Instant Quote</h1>
                                    <div>Quote No #: A00001</div>
                                    <div>Quote Date #: Jul 25, 2024</div>
                                    <div>Valid Till: Aug 09, 2024</div>
                                </td>
                                <td>
                                    <img src="https://arqonz.in/static/images/Arqonz-new-logo.png" alt="Arqonz Logo">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="addresssection">
                        <table>
                            <tr>
                                <td class="billedby">
                                    <div class="fromaddress">
                                        <h2>Billed By</h2>
                                        <div><b>ARQONZ GLOBAL PRIVATE LIMITED</b></div>
                                        <div class="companyinfo" style="font-size:12px;">
                                            <div>IIT Research Park, Taramani, Chennai, Tamil Nadu, India - 600113</div>
                                            <div><b>GSTIN:</b> 33AATCA8023B1ZX</div>
                                            <div><b>Phone:</b> +91 9150002745</div>
                                            
                                        </div>
                                    </div>
                                </td>
                                <td class="billedto">
                                    <div class="toaddress">
                                        <h2>Billed To</h2>
                                        <div><b>ABC Builders Ltd.</b></div>
                                            <div class="companyinfo" style="font-size:12px;">
                                            <div>No 218, Fountain Plaza, Pantheon Road, Chennai, Tamil Nadu, India - 600008</div>
                                            <div><b>GSTIN:</b> 24AACC1206D1ZM</div>
                                            <div><b>Phone:</b> +91 9150002745</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="tablesection">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Product Material</th>
                                    <th>Unit Price</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Sub-Total</th>
                                </tr>
                            </thead>
                            <tbody>';

        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $totalSum = 0;
        $serialNumber = 1;

        foreach ($products as $product) {
            $productName = $product->getProductName();
            $brand = $product->getBrand();
            $material = $product->getMaterial();
            $quantity = $product->getQuantity();

            $logger->info('Processing product', [
                'productName' => $productName,
                'brand' => $brand,
                'material' => $material,
                'quantity' => $quantity
            ]);

            $sql = "SELECT pr.ID FROM products pr
                    WHERE pr.Product_Type = :productName
                    AND pr.Product_Brand = :brand
                    AND pr.Product_Material = :material";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':productName', $productName);
            $stmt->bindValue(':brand', $brand);
            $stmt->bindValue(':material', $material);
            $stmt->execute();
            $productIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $logger->info('Product IDs found', ['productIds' => $productIds]);

            $minPrice = PHP_INT_MAX;
            $minPriceUnit = 'N/A';

            foreach ($productIds as $productId) {
                $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
                $priceStmt = $pdo->prepare($priceSql);
                $priceStmt->bindValue(':productId', $productId);
                $priceStmt->execute();
                $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

                $logger->info('Price data', ['productId' => $productId, 'priceData' => $priceData]);

                if ($priceData && $priceData['Product_Price'] < $minPrice) {
                    $minPrice = $priceData['Product_Price'];
                    $minPriceUnit = $priceData['Product_Unit'];
                }
            }

            $unitPrice = ($minPrice === PHP_INT_MAX) ? 'N/A' : $minPrice;
            $unit = $minPriceUnit;

            $logger->info('Final unit price and unit for product', [
                'unitPrice' => $unitPrice,
                'unit' => $unit
            ]);

            // Calculate the total price
            $totalPrice = ($unitPrice !== 'N/A') ? $unitPrice * $quantity : 'N/A';
            
            // Accumulate total sum
            if ($totalPrice !== 'N/A') {
                $totalSum += $totalPrice;
            }

            // Add product row to the table
            $html .= '<tr>
                        <td>' . $serialNumber . '</td>
                        <td style="text-align:left;">' . $productName . '</td>
                        <td style="text-align:left;">' . $brand . '</td>
                        <td style="text-align:left;">' . $material . '</td>
                        <td style="text-align:right;">₹' . $unitPrice . '</td>
                        <td>' . $unit . '</td>
                        <td>' . $quantity . '</td>
                        <td style="text-align:right;">₹' . $totalPrice . '</td>
                    </tr>';
            $serialNumber++;
        }

        // Add total row
        $html .= '
                            </tbody>
                        </table>
                    </div>
                    <div class="totalsection">
                        <div>Total (INR): ₹ ' . $totalSum . '</div>
                    </div>
                    <div class="disclaimer">
                        <em>Disclaimer: This is an AI-generated quote. Please verify the details for accuracy before proceeding with any transactions. Prices and availability are subject to change.</em>
                    </div>
                </div>
            </body>
            </html>';

        // $html .= '</tbody></table>';

        $html .= '</body></html>';

        // Load HTML content into DOMPDF
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $pdfOutput = $dompdf->output();

        // Prepare and send PDF as response
        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="InstantQuote.pdf"');

        $logger->info('PDF generated successfully');
        
        return $response;
    }


    /**
     * Index page for account - it is restricted to ROLE_USER via security annotation
     *
     * @Route("/account/indexNew", name="account-indexNew")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function architectDashboardActionNew(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $architectProfile = null;
            $architectActivate = null;

            // $form1 = $this->createForm(SearchFormType::class);
            // $form1->handleRequest($request);
            // if ($form1->isSubmitted() && $form->isValid()) {
            //     $formData1 = $form1->getData();
            //     $searchKeyword = $form1->get('Search')->getData();
            //     return $this->redirect("Search/$searchKeyword");
            // }
    
            
            if ($customertype === 'Customer'){

                return $this->render('account/customer_dashboard.html.twig', [
                    'customer' => $customer,
                ]);
            }
            elseif ($customertype === 'Contractor' || $customertype === 'Engineer' || $customertype === 'Designer' || $customertype === 'Architect' || $customertype === 'Builder' || $customertype === 'Manufacturer' || $customertype === 'Distributor' || $customertype === 'Retailer'){
                
                $PortfolioActivate = $customer->getPortfolioActivate();
                if($PortfolioActivate === 'true'){
                    $ProProfiles = $customer->getPortfolio();
                    $ProProfile = $ProProfiles[0];
                    

                    if($ProProfile){
                    
                        $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
                        $NotificationList->addConditionParam("professional = ?", $ProProfile);
            
                        $NotificationList->setOrderKey('creationDate');
                        $NotificationList->setOrder('desc');
        
                        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
                        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProProjectsList->setOrderKey('creationDate');
                        $ProProjectsList->setOrder('desc');

                        $ProProjects = $ProProjectsList->load();

                        $ProProducts = new \Pimcore\Model\DataObject\ProProduct\Listing();
                        $ProProducts->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProProducts->setOrderKey('creationDate');
                        $ProProducts->setOrder('desc');

                        $ProProducts = $ProProducts->load();

                        // $form = $this->createForm(ProRequirementFormType::class);
                        // $form->handleRequest($request);

                        // if ($form->isSubmitted() && $form->isValid()) {
                        //     // Handle file upload and save the ProRequirement object

                        //     $uploadedFile = $form->get('excelFile')->getData();
                        //     $proRequirement = new ProRequirement();

                
                        //     try {
                        //         $asset = new Document();
                        //         $asset->setData(file_get_contents($uploadedFile->getPathname()));
                        //         $timestamp = time();
                        //         $originalFilename = $uploadedFile->getClientOriginalName();
                        //         $newFilename = $timestamp . '_' . $originalFilename;
                        //         $asset->setFilename($newFilename); // Set the desired filename

                        //         // Save the asset in the "/Services/Requirements" directory
                        //         if ($customertype === 'Contractor'){
                        //             $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/Requirements"));
                        //             }
                        //         elseif ($customertype === 'Designer'){
                        //             $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/Requirements"));
                        //             }
                        //         elseif ($customertype === 'Architect'){
                        //             $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/Requirements"));
                        //             }
                        //         elseif ($customertype === 'Builder'){
                        //             $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/Requirements"));
                        //             }
                                
                        //         $asset->save();
                        //         $proRequirement->setExcelFile($asset);
                        //         $objkey = time();
                        //         $proRequirement->setKey(time());
                        //         $proRequirement->setParent(Service::createFolderByPath('/Requirements'));
                        //         $proRequirement->setTitle($form->get('Title')->getData());
                                
                        //         $proRequirement->setDescription($form->get('Description')->getData());
                        //         $proRequirement->setProfessional($ProProfile);
                        //         $proRequirement->setProfessionalPath($ProProfile);
                        //         $excelData = $this->processExcelData($uploadedFile);
                        //         $proRequirement->setExcelData($excelData);
                        //         $proRequirement->setObjeKey($proRequirement->getKey());
                        //         $proRequirement->setPublished(true);
                
                        //         $proRequirement->save();
            
                        //         // Redirect or do other actions
                        //         $this->addFlash('success', $translator->trans('Requirements submitted succesfully.'));
                        //     } catch (FileException $e) {
                        //         // Handle file upload error
                        //         // Log the error or show a flash message to the user
                        //     }
                        // }

                        

                        $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
                        $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProRequirementsList->setOrderKey('creationDate');
                        $ProRequirementsList->setOrder('desc');

                        $ProRequirements = $ProRequirementsList->load();
                        
                        // Load ProEnquiries

                        $ProEnquiryList = new \Pimcore\Model\DataObject\ProEnquiry\Listing();
                        $ProEnquiryList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProEnquiryList->setOrderKey('creationDate');
                        $ProEnquiryList->setOrder('desc');

                        $ProEnquiries = $ProEnquiryList->load();
                    }

                    
                    return $this->render('Professional/Dashboard/dashboard.html.twig', [
                        'ProProfile' => $ProProfile,
                        'ProProjects' => $ProProjects,
                        'ProProducts' => $ProProducts,
                        'customer' => $customer,
                        // 'form' => $form->createView(),
                        'Requirements' => $ProRequirements,
                        'ProEnquiries' => $ProEnquiries,
                        // 'form1' => $form1->createView(),
                                           
                    ]);
                }
                else{
                    return $this->render('account/dashboard.html.twig', [
                        'customer' => $customer,
                        'form' => $form->createView(),
                    ]);
                }
            }
        }
    
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    // Add a method to read Excel data and convert it to the required format
    private function processExcelData(UploadedFile $file)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();

        $excelData = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $excelData[] = $rowData;
        }

        $structuredTable = new \Pimcore\Model\DataObject\Data\StructuredTable();
        $tableData = [];
        foreach ($excelData as $row) {
            $rowData = [];
            foreach ($row as $value) {
                $rowData[] = $value;
            }
            $tableData[] = $rowData;
        }
        return $tableData;
    }


    /**
     * @Route("BOQ/customize/{url}", name="BOQ_Customize")
     */
    public function BOQCustomizeAction($url, Security $security, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            // Load ArchitectProfile based on the URL
            $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
           

            if (!$ProRequirement) {
                throw $this->createNotFoundException('Requirement not found');
            }

            //Fetch pro RequirementProducts
            $ProRequirementProducts = $ProRequirement->getProRequirementProduct();
            usort($ProRequirementProducts, function($a, $b) {
                return $b->getCreationDate() <=> $a->getCreationDate();
            });

            $ProProfile = $ProRequirement->getProfessional();

            
            $form1 = $this->createForm(SearchFormType::class);
            $form1->handleRequest($request);
            if ($form1->isSubmitted() && $form->isValid()) {
                $formData1 = $form1->getData();
                $searchKeyword = $form1->get('Search')->getData();
                return $this->redirect("Search/$searchKeyword");
            }

            
            

            return $this->render('Professional/Dashboard/Dashboard_Customize_BOQ.html.twig', [
                'ProProfile' => $ProProfile,
                'ProRequirement' => $ProRequirement,
                'ProRequirementProducts' => $ProRequirementProducts,
                'customer' => $customer,
                'form1' => $form1->createView(),
                

            ]);
        }
    
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    
    /**
     * @Route("/supplier-pinned-notification", name="add-supplier-pinned-notification", methods={"POST"})
     */
    public function addSupplierPinnedNotification(Request $request, Security $security, LoggerInterface $logger): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $ProductName = $request->request->get('productName');
            $brandName = $request->request->get('brandName');
            $specification = $request->request->get('specification');
            $productUnit = $request->request->get('productUnit');
            $productQuantity = $request->request->get('quantity');
            $location = $request->request->get('location');
            $expiryDate = $request->request->get('expiryDate');
            $ProRequirementProductPath = $request->request->get('ProRequirementProductPath');
            $ProductMaterial = $request->request->get('Material');

            $logger->info('Request data received', [
                'productName' => $ProductName,
                'brandName' => $brandName,
                'specification' => $specification,
                'productUnit' => $productUnit,
                'productQuantity' => $productQuantity,
                'location' => $location,
                'expiryDate' => $expiryDate,
                'ProRequirementProductPath' => $ProRequirementProductPath,
                'ProductMaterial' => $ProductMaterial
            ]);

            $proRequirementProduct = ProRequirementProduct::getByPath($ProRequirementProductPath);
            $proRequirementProduct->setQuoteStatus('Started');
            if ($expiryDate) {
                try {
                    $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                    $expiryCarbonDate = Carbon::instance($expiryDateTime);
                    $proRequirementProduct->setEndDate($expiryCarbonDate);
                } catch (\Exception $e) {
                    error_log("Error converting expiry date: " . $e->getMessage());
                    // Handle the error, maybe log it or return an error response
                    // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
                }
            }
            $proRequirementProduct->save();

            // Retrieve all ProProfiles with PortfolioType "Manufacturer"
            $list = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $list->setCondition('PortfolioType = ?', ['Manufacturer']);
            $proProfiles = $list->load();

            $logger->info('Loaded ProProfiles', ['count' => count($proProfiles)]);

            // Loop through each ProProfile
            foreach ($proProfiles as $proProfile) {
                $logger->info('Processing ProProfile', ['proProfileId' => $proProfile->getId()]);
                // Retrieve all ProProduct objects linked to the current ProProfile
                $proProducts = $proProfile->getProducts();

                $logger->info('Loaded ProProducts for ProProfile', [
                    'proProfileId' => $proProfile->getId(),
                    'proProductCount' => count($proProducts)
                ]);

                foreach ($proProducts as $proProduct) {
                    // Split the tags into an array
                    $tags = explode(',', $proProduct->getTags());
                    $tags = array_map('trim', $tags);
                    $tags = array_map('strtolower', $tags);
                    $tags = array_unique($tags);

                    $logger->info('Processing ProProduct', [
                        'proProductId' => $proProduct->getId(),
                        'tags' => $tags
                    ]);

                    // Convert ProductName to lowercase for comparison
                    $lowercaseProductName = strtolower(trim($ProductName));

                    // Check if any tag matches the ProductName
                    // if (in_array(trim($ProductName), array_map('trim', $tags))) {
                    foreach ($tags as $tag) {
                        if ($lowercaseProductName === $tag) {

                            $logger->info('Match found', ['proProductId' => $proProduct->getId(), 'tag' => $tag]);
                            
                            // Create a new SupplierPinnedNotification for each matching tag
                            $supplierPinnedNotification = new SupplierPinnedNotification();
                            $supplierPinnedNotification->setBrand($brandName);
                            $supplierPinnedNotification->setProductName($ProductName);
                            $supplierPinnedNotification->setSpecification($specification);
                            $supplierPinnedNotification->setProductUnit($productUnit);
                            $supplierPinnedNotification->setProductQuantity($productQuantity);
                            $supplierPinnedNotification->setLocation($location);
                            $supplierPinnedNotification->setProRequirementProduct($proRequirementProduct);
                            $supplierPinnedNotification->setMaterial($ProductMaterial);
                            $supplierPinnedNotification->setSupplier($proProfile); // Set the Supplier field to the current ProProfile

                            if ($expiryDate) {
                                try {
                                    $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                                    $expiryCarbonDate = Carbon::instance($expiryDateTime);
                                    $supplierPinnedNotification->setEndDate($expiryCarbonDate);
                                } catch (\Exception $e) {
                                    error_log("Error converting expiry date: " . $e->getMessage());
                                    // Handle the error, maybe log it or return an error response
                                    // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
                                }
                            }
                            

                            // if ($expiryDate) {
                            //     $expiryDate = Carbon::instance($expiryDate)->setTimezone('Asia/Kolkata');
                            //     $supplierPinnedNotification->setEndDate($expiryDate);
                            // }

                            $supplierPinnedNotification->setParent(Service::createFolderByPath('/PinnedNotifications'));
                            $supplierPinnedNotification->setKey(uniqid());
                            $supplierPinnedNotification->setPublished(true);
                            $supplierPinnedNotification->save();

                            $logger->info('Created and saved SupplierPinnedNotification', ['supplierPinnedNotificationId' => $supplierPinnedNotification->getId()]);
                        }
                    }
                }
            }

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }


    /**
     * @Route("/check-unlock-status", name="check_unlock_status", methods={"POST"})
     */
    public function checkUnlockStatus(Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {
        $user = $security->getUser();
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['bidKey'])) {
                $bidKey = $data['bidKey'];
                $logger->info('Bid Key received for status check', ['bidKey' => $bidKey]);

                $supplierBid = SupplierBid::getByPath('/SupplierBid/' . $bidKey);
                if (!$supplierBid instanceof SupplierBid) {
                    return new JsonResponse(['success' => false, 'message' => 'Invalid bid key'], JsonResponse::HTTP_BAD_REQUEST);
                }

                // Check if the bid is already unlocked
                if ($supplierBid->getQuoteStatus() === 'Unlocked') {
                    $supplier = $supplierBid->getSupplier();
                    $phoneNumber = $supplier->getPhoneNumber();
                    $CompanyName = $supplier->getCompanyName();
                    $ProfileUrl = '/manufacturer/portfolio/'.$supplier->getKey();



                    return new JsonResponse([
                        'success' => true,
                        'phoneNumber' => $phoneNumber,
                        'message' => 'Contact already unlocked',
                        'CompanyName' => $CompanyName,
                        'ProfileUrl' => $ProfileUrl,


                    ]);
                } else {
                    return new JsonResponse(['success' => false, 'message' => 'Contact not unlocked'], JsonResponse::HTTP_OK);
                }
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Bid key not provided'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }


    /**
     * @Route("/unlock-supplier-contact", name="unlock_supplier_contact", methods={"POST"})
     */
    public function unlockSupplierContact(Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {
        $user = $security->getUser();
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['bidKey'])) {
                $bidKey = $data['bidKey'];
                $logger->info('Bid Key received', ['bidKey' => $bidKey]);

                $supplierBid = SupplierBid::getByPath('/SupplierBid/' . $bidKey);
                if (!$supplierBid instanceof SupplierBid) {
                    return new JsonResponse(['success' => false, 'message' => 'Invalid bid key'], JsonResponse::HTTP_BAD_REQUEST);
                }

                if ($supplierBid->getQuoteStatus() === 'Unlocked') {
                    $supplier = $supplierBid->getSupplier();
                    $phoneNumber = $supplier->getPhoneNumber();

                    return new JsonResponse([
                        'success' => true,
                        'phoneNumber' => $phoneNumber,
                        'message' => 'Contact already unlocked'
                    ]);
                }

                if ($user->getCreditPoints() > 0) {
                    $creditBalance = $user->getCreditPoints();
                    $user->setCreditPoints($creditBalance - 1);
                    $user->save();
                    

                    $supplierBid->setQuoteStatus('Unlocked');
                    $supplierBid->save();

                    $supplier = $supplierBid->getSupplier();
                    $phoneNumber = $supplier->getPhoneNumber();

                    return new JsonResponse([
                        'success' => true,
                        'phoneNumber' => $phoneNumber,
                        'message' => 'Contact unlocked successfully'
                    ]);
                } else {
                    return new JsonResponse(['success' => false, 'message' => 'Insufficient credits'], JsonResponse::HTTP_BAD_REQUEST);
                }
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Bid key not provided'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }



    // /**
    //  * @Route("/api/Bid-Accepted", name="add-supplier-Bid-Accepted", methods={"POST"})
    //  */
    // public function createSupplierBid(Request $request, Security $security): Response
    // {
    //     $user = $security->getUser();
    // }


    /**
     * @Route("/api/supplier-bid", name="add-supplier-Bid", methods={"POST"})
     */
    public function createSupplierBid(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $productName = $request->request->get('productName');
            $productBrand = $request->request->get('productBrand');
            $productQuantity = $request->request->get('productQuantity');
            $productUnit = $request->request->get('productUnit');
            $productMaterial = $request->request->get('productMeterial');
            $bidAmount = $request->request->get('bidAmount');
            $supplierPinnedNotificationPath = $request->request->get('SupplierPinnedNotificationPath');
            $expiryDate = $request->request->get('deliveryTime');

            $supplierPinnedNotification = SupplierPinnedNotification::getByPath($supplierPinnedNotificationPath);

            

            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }

            if (!empty($ProProfiles)) {

                $ProProfile = $ProProfiles[0];
                    
                // Create a new SupplierPinnedNotification for each matching tag
                $supplierBid = new SupplierBid();
                $supplierBid->setProductName($productName);
                $supplierBid->setProductBrand($productBrand);
                $supplierBid->setProductQuantity($productQuantity);
                $supplierBid->setProductUnit($productUnit);
                $supplierBid->setMaterial($productMaterial);
                $supplierBid->setBidAmount($bidAmount);
                $supplierBid->setSupplierPinnedNotification($supplierPinnedNotification);
                $supplierBid->setProRequirementProduct($supplierPinnedNotification->getProRequirementProduct());
                $supplierBid->setSupplier($ProProfile); // Set the Supplier field to the current ProProfile
            

                // if ($expiryDate) {
                //     $expireDate = Carbon::instance($expireDate)->setTimezone('Asia/Kolkata');
                //     $supplierBid->setEndDate($expiryDate);
                // }

                if ($expiryDate) {
                    try {
                        $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                        $expiryCarbonDate = Carbon::instance($expiryDateTime);
                        $supplierBid->setEndDate($expiryCarbonDate);
                    } catch (\Exception $e) {
                        error_log("Error converting expiry date: " . $e->getMessage());
                        // Handle the error, maybe log it or return an error response
                        // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
                    }
                }

                $supplierBid->setParent(Service::createFolderByPath('/SupplierBid'));
                $supplierBid->setKey(uniqid());
                $supplierBid->setPublished(true);
                $supplierBid->save();

                $supplierPinnedNotification->setStatus('Accepted');
                $supplierPinnedNotification->setCurrentBid($supplierBid);
                $supplierPinnedNotification->save();

            }

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/api/edit-supplier-bid", name="edit-supplier-bid", methods={"POST"})
     */
    public function editSupplierBid(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $productName = $request->request->get('Edit-productNameField');
            $productBrand = $request->request->get('Edit-productBrandField');
            $productQuantity = $request->request->get('Edit-productQuantityField');
            $productUnit = $request->request->get('Edit-productUnitField');
            $productMaterial = $request->request->get('Edit-productMeterial');
            $bidAmount = $request->request->get('Edit-bidAmount');
            $expiryDate = $request->request->get('EditdeliveryTime');
            $DeliveryLocation = $request->request->get('Edit-productLocation');
            $supplierPinnedNotificationPath = $request->request->get('Edit-SupplierPinnedNotificationPath');

            $supplierPinnedNotification = SupplierPinnedNotification::getByPath($supplierPinnedNotificationPath);
            $supplierBid = $supplierPinnedNotification->getCurrentBid();

            if ($supplierBid) {
                $supplierBid->setProductName($productName);
                $supplierBid->setProductBrand($productBrand);
                $supplierBid->setProductQuantity($productQuantity);
                $supplierBid->setProductUnit($productUnit);
                $supplierBid->setMaterial($productMaterial);
                $supplierBid->setBidAmount($bidAmount);

                if ($expiryDate) {
                    try {
                        $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                        $expiryCarbonDate = Carbon::instance($expiryDateTime);
                        $supplierBid->setEndDate($expiryCarbonDate);
                    } catch (\Exception $e) {
                        error_log("Error converting expiry date: " . $e->getMessage());
                        return new JsonResponse(['success' => false, 'message' => 'Invalid date format'], 400);
                    }
                }

                $supplierBid->save();

                $supplierPinnedNotification->setCurrentBid($supplierBid);
                $supplierPinnedNotification->save();

                return new JsonResponse(['success' => true]);
            }

            return new JsonResponse(['success' => false, 'message' => 'Bid not found'], 404);
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }


    /**
     * @Route("/supplier-pinned-notification/delete/{key}", name="delete-supplier-pinned-notification", methods={"DELETE"})
     */
    public function deleteSupplierPinnedNotification(string $key): Response
    {
        $path = "/PinnedNotifications/{$key}";
        $supplierPinnedNotification = SupplierPinnedNotification::getByPath($path);

        if ($supplierPinnedNotification) {
            $supplierPinnedNotification->delete();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Notification not found'], JsonResponse::HTTP_NOT_FOUND);
    }




    /**
     * @Route("BOQ/3D-product-demo", name="3d-Product-demo")
     */
    public function productdemo3d(Security $security, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        
    
        // If the user is not an architect or the architect is not activated
        return $this->render('Professional/product-demo.html.twig');
    }


    /**
     * @Route("/fetch-brands", name="fetch_brands_api", methods={"POST"})
     */
    public function fetchBrands(Request $request, LoggerInterface $logger): Response
    {
        try {
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';

            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $productType = $request->request->get('product_type');
            $optionType = $request->request->get('option_type');

            if ($optionType === 'brands') {
                $sql = "SELECT DISTINCT Product_Brand FROM products WHERE Product_Type = :productType";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productType', $productType);
                $stmt->execute();
                $options = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            } elseif ($optionType === 'specifications') {
                $sql = "SELECT Specification_Number, Specifying_Factor FROM product_specification WHERE Product_ID = (SELECT Product_ID FROM products WHERE Product_Name = :productType LIMIT 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productType', $productType);
                $stmt->execute();
                $options = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } elseif ($optionType === 'size') {
                $brand = $request->request->get('brand');
                $sql = "SELECT DISTINCT Specification_1 FROM products WHERE Product_Type = :productType AND Product_Brand = :brand";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productType', $productType);
                $stmt->bindValue(':brand', $brand);
                $stmt->execute();
                $options = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            } elseif ($optionType === 'specification_values') {
                $specificationNumber = $request->request->get('specification_number');
                $column = "Specification_" . $specificationNumber;
                $sql = "SELECT DISTINCT $column FROM products WHERE Product_ID = (SELECT Product_ID FROM products WHERE Product_Type = :productType LIMIT 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productType', $productType);
                $stmt->execute();
                $options = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            } else {
                throw new \InvalidArgumentException('Invalid option type.');
            }

            return $this->json($options);
        } catch (\Exception $e) {
            $logger->error('Error fetching options: ' . $e->getMessage());
            return new Response('Error fetching options: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * 
     *
     * @Route("/account/Profile", name="account-Profile")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardProfileAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }
            

            return $this->render('Professional/dashboard_Profile.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * @Route("/account/Profile/save", name="save-Profile-details", methods={"POST"})
     */
    public function saveProfiledetailsAction(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            if ($customer->getPortfolioActivate() === 'true') {
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];

                // Assign form data to ProProfile fields only if not empty
                if ($FirstName = $request->request->get('first-name')) {
                    $customer->setfirstname($FirstName);
                }
                if ($LastName = $request->request->get('last-name')) {
                    $customer->setlastname($LastName);
                }
                if ($email = $request->request->get('email')) {
                    $customer->setemail($email);
                }
                if ($DOB = $request->request->get('dob')) {
                    $DOB = Carbon::instance($DOB)->setTimezone('Asia/Kolkata');
                    $customer->setDOB($DOB);
                }
                if ($StreetAddress = $request->request->get('street-address')) {
                    $customer->setstreet($StreetAddress);
                }
                if ($City = $request->request->get('city')) {
                    $customer->setcity($City);
                }
                if ($state = $request->request->get('state')) {
                    $customer->setState($state);
                }
                if ($Country = $request->request->get('country')) {
                    $customer->setCountry($Country);
                }
                if ($Pincode = $request->request->get('pincode')) {
                    $customer->setzip($pincode);
                }

                // Save the ProProfile object only if changes were made
                $customer->save();
            }
        }

        return new Response('Profile details saved successfully.', Response::HTTP_OK);
    }



    /**
     * 
     *
     * @Route("/account/Profile/Professional-details", name="Professional-Profile-details")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function ProfessionalProfileAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }



            return $this->render('Professional/dashboard_Profile_Professional.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/account/Profile/Professional-details/save", name="save-Professional-details", methods={"POST"})
     */
    public function saveProfessionaldetailsAction(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            if ($customer->getPortfolioActivate() === 'true') {
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];

                // Assign form data to ProProfile fields only if not empty
                if ($Education = $request->request->get('Prof-Education')) {
                    $customer->setEducation($Education);
                }
                if ($Specialization = $request->request->get('prof-Specialization')) {
                    $customer->setSpecialization($Specialization);
                }
                if ($Awards = $request->request->get('Awards-honors')) {
                    $customer->setAwards($Awards);
                }
                if ($Certification = $request->request->get('Certification-licences')) {
                    $customer->setSkills($Certification);
                }

                // Save the ProProfile object only if changes were made
                $customer->save();
            }
        }

        return new Response('Profile details saved successfully.', Response::HTTP_OK);
    }



    /**
     * 
     *
     * @Route("/account/Profile/Company-details", name="Company-Profile-details")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function CompanyProfileAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }



            return $this->render('Professional/dashboard_Profile_Company.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/account/Profile/Company-details/save", name="save-company-profile-details", methods={"POST"})
     */
    public function saveCompanyProfileAction(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            if ($customer->getPortfolioActivate() === 'true') {
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];

                // Assign form data to ProProfile fields only if not empty
                if ($companyName = $request->request->get('Company-name')) {
                    $ProProfile->setCompanyName($companyName);
                }
                if ($gstNumber = $request->request->get('GST-Number')) {
                    $ProProfile->setGstnumber($gstNumber);
                }
                if ($description = $request->request->get('Company-Description')) {
                    $ProProfile->setDescription($description);
                }
                if ($skills = $request->request->get('Skills')) {
                    $ProProfile->setSkills($skills);
                }
                if ($specialization = $request->request->get('Specialization')) {
                    $ProProfile->setSpecialization($specialization);
                }
                if ($citiesServed = $request->request->get('Cities-Served')) {
                    $ProProfile->setCitiesServed($citiesServed);
                }
                if ($yearEstablished = $request->request->get('Year-Established')) {
                    $ProProfile->setYearEstablished($yearEstablished);
                }
                if ($pricePerHour = $request->request->get('price-per-hour')) {
                    $ProProfile->setPriceForHour($pricePerHour);
                }
                if ($streetAddress = $request->request->get('Company-Street-Address')) {
                    $ProProfile->setStreetAddress($streetAddress);
                }
                if ($city = $request->request->get('Company-City')) {
                    $ProProfile->setCity($city);
                }
                if ($state = $request->request->get('Company-State')) {
                    $ProProfile->setState($state);
                }
                if ($country = $request->request->get('Company-Country')) {
                    $ProProfile->setCountry($country);
                }
                if ($pinCode = $request->request->get('Company-pin-code')) {
                    $ProProfile->setPinCode($pinCode);
                }
                if ($companyWebsite = $request->request->get('Company-Website')) {
                    $ProProfile->setCompanyWebsite($companyWebsite);
                }

                // COA Number field (conditionally saved if customer type is Architect)
                if ($customer->getcustomertype() == 'Architect' && $coaNumber = $request->request->get('COA-Number')) {
                    $ProProfile->setCoaNumber($coaNumber);
                }

                // Handle Profile Image upload
                $imageData = $request->files->get('CoverPicture');
                dump($imageData);

                if ($imageData) {
                    $previousimage = $ProProfile->getProfileImage();
                    if ($previousimage) {
                        $previousimage->delete();
                    }

                    $imageName = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $imageData->getClientOriginalExtension();
                    $newAsset = new \Pimcore\Model\Asset\Image();
                    $newAsset->setFilename($imageName);

                    $customertype = $customer->getcustomertype();
                    $folderPath = "/Services/" . ucfirst($customertype) . "s/ProfileGallery";
                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath($folderPath));

                    $newAsset->setData(file_get_contents($imageData->getPathname()));
                    $newAsset->save();
                    $ProProfile->setProfileImage($newAsset);
                }

                // Save the ProProfile object only if changes were made
                $ProProfile->save();
            }
        }

        return new Response('Profile details saved successfully.', Response::HTTP_OK);
    }


    /**
     * 
     *
     * @Route("/account/Profile/social-media", name="SocialMedia-Profile-details")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function SocialMediaProfileAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }



            return $this->render('Professional/dashboard_socialmedia_profile.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/account/Profile/social-media/save", name="save-social-media-details", methods={"POST"})
     */
    public function savesocialmediadetailsAction(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            if ($customer->getPortfolioActivate() === 'true') {
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];

                // Assign form data to ProProfile fields only if not empty
                if ($LinkedIn = $request->request->get('Linked-In')) {
                    $customer->setLinkedIn($LinkedIn);
                }
                if ($Facebook = $request->request->get('Facebook-profile')) {
                    $customer->setFacebook($Facebook);
                }
                if ($Twitter = $request->request->get('twitter-profile')) {
                    $customer->setTwitter($Twitter);
                }
                if ($Instagram = $request->request->get('Instagram-profile')) {
                    $customer->setInstagram($Instagram);
                }

                // Save the ProProfile object only if changes were made
                $customer->save();
            }
        }

        return new Response('Profile details saved successfully.', Response::HTTP_OK);
    }



    /**
     * @Route("/delete-asset", name="delete_asset_api", methods={"POST"})
     */
    public function deleteAsset(Request $request): Response
    {
        // Get the asset ID from the request parameters
        $assetId = $request->request->get('assetId');

        // Load the asset object
        $asset = Asset::getById($assetId);

        // Check if the asset exists
        if ($asset instanceof Asset) {
            // Delete the asset
            $asset->delete();
            return new Response('Asset deleted successfully', Response::HTTP_OK);
        } else {
            return new Response('Asset not found', Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * @Route("/proposal-bid", name="add-proposal-bid", methods={"POST"})
     */
    public function AddProposalBid(Request $request, Security $security): Response
    {   
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            // Get the asset ID from the request parameters
            $BidAmount = $request->request->get('BidAmount');
            $DeliveryTime = $request->request->get('DeliveryTime');
            
            $customer = $user;
            // $proRequirementProduct = $request->request->get('ProRequirementProduct');
            $proRequirementProduct = ProRequirementProduct::getByPath( $request->request->get('ProRequirementProduct'));
            $proRequirement = $proRequirementProduct->getProRequirement();
            $ProProposalBid = new ProProposalBid();
            $ProProposalBid->setBidAmount($BidAmount);
            $ProProposalBid->setProRequirementProduct($proRequirementProduct);
            $ProProposalBid->setProRequirement($proRequirement);
            
            $ProProposalBid->setcustomer($customer);


            $ProProposalBid->setParent(Service::createFolderByPath('/Services/Manufacturers/ProProposalProducts/'));
            $ProProposalBid->setKey(uniqid());
            $ProProposalBid->setPublished(true);
            $ProProposalBid->save();

            
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * 
     *
     * @Route("/account/Portfolio", name="account-Portfolio")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardPortfolioAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->setOrderKey('creationDate');
            $NotificationList->setOrder('desc');

            $form = null;

            if (!empty($ProProfiles)) {
                $ProProfile = $ProProfiles[0];

                if ($customertype === 'Contractor'){
                    $form = $this->createForm(ContractorRegistrationFormType::class);
                    }
                elseif ($customertype === 'Designer'){
                    $form = $this->createForm(DesignerRegistrationFormType::class);
                    }
                elseif ($customertype === 'Architect'){
                    $form = $this->createForm(ArchitectRegistrationFormType::class);
                    }
                elseif ($customertype === 'Builder'){
                    $form = $this->createForm(BuilderProfileUpdateFormType::class);
                    }
                elseif ($customertype === 'Manufacturer'){
                    $form = $this->createForm(ManufacturerRegistrationFormType::class);
                    }
                elseif ($customertype === 'Distributor'){
                    $form = $this->createForm(DistributorRegistrationFormType::class);
                    }
                elseif ($customertype === 'Engineer'){
                    $form = $this->createForm(EngineerRegistrationFormType::class);
                    }
                elseif ($customertype === 'Retailer'){
                    $form = $this->createForm(RetailerRegistrationFormType::class);
                    }
                foreach ($form->all() as $formField) {
                    $fieldName = $formField->getName();
            
                    // Exclude ProfileImage field
                    if ($fieldName !== 'ProfileImage' && $fieldName !== 'CitiesServed' && $fieldName !== 'Skills' && $fieldName !== '_submit') {
                        $formField->setData($ProProfile->{'get' . ucfirst($fieldName)}());
                    } elseif ($fieldName === 'CitiesServed') {
                        // Handle CitiesServed transformation
                        $citiesServed = $ProProfile->getCitiesServed(); // Assuming a method like getCitiesServed() exists
                        $formField->setData(explode(',', $citiesServed));
                    }
                } 

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $ProProfile->setCompanyName($form->get('CompanyName')->getData());
                    if ($customertype !== 'Manufacturer' && $customertype !== 'Distributor') {
                        
                        if($form->has('Specialization') && $form->get('Specialization')->getData()){
                            $ProProfile->setspecialization($form->get('Specialization')->getData());
                        }
                        if($form->has('YearOfCertification') && $form->get('YearOfCertification')->getData()){
                            $ProProfile->setYearOfCertification($form->get('YearOfCertification')->getData());
                        }
                        if($form->has('Skills')){
                            $skillsData = $form->get('Skills')->getData();
                            if (is_array($skillsData)) {
                                // Skills submitted as Select2 (array), implode them
                                $skillsString = implode(',', $skillsData);
                            } else {
                                // Skills submitted as manually typed text, use as is
                                $skillsString = $skillsData;
                            }
                            $ProProfile->setSkills($skillsString);
                        }
                    }
                    
                    if($form->has('gstnumber') && $form->get('gstnumber')->getData()){
                        $ProProfile->setgstnumber($form->get('gstnumber')->getData());
                    }
                    if($form->has('Brands') && $form->get('Brands')->getData()){
                        $ProProfile->setBrands($form->get('Brands')->getData());
                    }
                    $ProProfile->setYearEstablished($form->get('YearEstablished')->getData());
                    
                    if ($customertype == 'Architect') {
                        if($form->get('CoaNumber')->getData()){
                            $ProProfile->setCoaNumber($form->get('CoaNumber')->getData());
                        }
                    }
                    $imageData = $form->get('ProfileImage')->getData();

                    if ($imageData) {
                        if($customer->getPortfolioActivate() === 'true'){
                            $previousimage = $ProProfile->getProfileImage();
                            if ($previousimage) {
                                $previousimage->delete();
                            }
                            
                        }
                        

                        $imageName = $imageData->getClientOriginalName();
                        $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                        $newAsset = new Image();
                        
                        $newAsset->setFilename($imageName);
                        if ($customertype == 'Contractor') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProfileGallery"));
                        }
                        if ($customertype == 'Designer') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProfileGallery"));
                        }
                        if ($customertype == 'Architect') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/ProfileGallery"));
                        }
                        if ($customertype == 'Builder') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/ProfileGallery"));
                        }
                        if ($customertype == 'Manufacturer') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProfileGallery"));
                        }
                        if ($customertype == 'Engineer') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/ProfileGallery"));
                        }
                        if ($customertype == 'Distributor') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProfileGallery"));
                        }
                        if ($customertype == 'Retailer') {
                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProfileGallery"));
                        }
                    
                        $newAsset->setData(file_get_contents($imageData->getPathname()));
                        $newAsset->save();
                        $ProProfile->setProfileImage($newAsset);
                    }

                    
                    $ProProfile->setCitiesServed(implode(',', $form->get('CitiesServed')->getData()));
                    $ProProfile->setDescription($form->get('Description')->getData());

                    if ($customertype !== 'Builder') {
                        if ($customertype !== 'Manufacturer' && $customertype !== 'Distributor') {
                            if($form->get('PriceForHour')->getData()){
                                $ProProfile->setPriceForHour($form->get('PriceForHour')->getData());
                            }
                        }
                    }

                    

                    $ProProfile->setCountryCode($form->get('CountryCode')->getData());
                    $ProProfile->setPhoneNumber($form->get('PhoneNumber')->getData());
                    $ProProfile->setStreetAddress($form->get('StreetAddress')->getData());
                    $ProProfile->setCity($form->get('City')->getData());
                    $ProProfile->setState($form->get('State')->getData());
                    $ProProfile->setCountry($form->get('Country')->getData());
                    $ProProfile->setPinCode($form->get('PinCode')->getData());
                    $ProProfile->setPortfolioType($customertype);
                    $ProProfile->setPublished(true);

                    $ProProfile->save();
                }

                return $this->render('Professional/dashboard_Portfolio.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'form' => $form->createView(),
                ]);
            }
        }
        
        // If the user is not logged in or doesn't have the required role
        return $this->render('Architect/NotLogged_signup.html.twig');
    }
    

    
    /**
     * 
     *
     * @Route("/account/Projects", name="account-Projects")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardProjectsAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->setOrderKey('creationDate');
            $NotificationList->setOrder('desc');

            $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
            $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProProjectsList->setOrderKey('creationDate');
            $ProProjectsList->setOrder('desc');

            $ProProjects = $ProProjectsList->load();


            return $this->render('Professional/dashboard_Projects.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProProjects' => $ProProjects,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * 
     *
     * @Route("/account/Products", name="account-Products-list")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardProductsAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->setOrderKey('creationDate');
            $NotificationList->setOrder('desc');

            $ProProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
            $ProProductsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProProductsList->setOrderKey('creationDate');
            $ProProductsList->setOrder('desc');

            $ProProducts = $ProProductsList->load();


            return $this->render('Professional/dashboard_products_list.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProProducts' => $ProProducts,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * 
     *
     * @Route("/account/Project/edit/{url}", name="account-Project-edit")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardProjectEditAction($url, Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
        

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $userurl = $customertype . 's';
            
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->setOrderKey('creationDate');
            $NotificationList->setOrder('desc');

            if ($customertype === 'Contractor'){
                $form = $this->createForm(ContractorEditProjectFormType::class);
                
                $ProProject = ProProject::getByPath("/Services/Contractors/Projects/$url");
                }
            elseif ($customertype === 'Designer'){
                $form = $this->createForm(DesignerEditProjectFormType::class);
                $ProProject = ProProject::getByPath("/Services/Designers/Projects/$url");
                }
            elseif ($customertype === 'Architect'){
                $form = $this->createForm(ArchitectEditProjectFormType::class);
                $ProProject = ProProject::getByPath("/Services/Architects/Projects/$url");
                }
            elseif ($customertype === 'Builder'){
                $form = $this->createForm(BuilderEditProjectFormType::class);
                $ProProject = ProProject::getByPath("/Services/Builders/Projects/$url");
                }
            elseif ($customertype === 'Engineer'){
                $form = $this->createForm(EngineerAddProjectFormType::class);
                $ProProject = ProProject::getByPath("/Services/Builders/Projects/$url");
                }
            foreach ($form->all() as $formField) {
                $fieldName = $formField->getName();
        
                // Exclude ProfileImage field
                if ($fieldName !== 'ProjectGallery' && $fieldName !== '_submit' && $fieldName !== 'FloorMaps' && $fieldName !== 'ReraApproval') {
                    $formField->setData($ProProject->{'get' . ucfirst($fieldName)}());
                } elseif ($fieldName === 'ReraApproval') {
                    // Convert the ReraApproval value to boolean
                    $reraApprovalValue = $ProProject->getReraApproval();
                    $reraApprovalBoolean = $reraApprovalValue === '1';
                    // Set the form field with the boolean value
                    $formField->setData($reraApprovalBoolean);
                }
            }
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProProject->setProfessional($ProProfile);

                $ProProject->setProjectName($form->get('ProjectName')->getData());
                $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                $ProProject->setLocation($form->get('Location')->getData());
                $ProProject->setMinPrice($form->get('MinPrice')->getData());
                $ProProject->setConfiguration($form->get('Configuration')->getData());
                $ProProject->setCollaborations($form->get('Collaborations')->getData());
                $ProProject->setProfessionalPath($ProProfile);
                
                
                $galleryData = $form->get('ProjectGallery')->getData();
                if($customertype === 'Builder'){
                    //FloorMap Handling
                    $FloormapData =  $form->get('FloorMaps')->getData();
                    if($FloormapData){

                        $existingFloormaps = $ProProject->getFloorMaps();
                        // Delete old ImageGallery assets
                        if ($existingFloormaps instanceof ImageGallery) {
                            foreach ($existingFloormaps->getItems() as $item) {
                                $image = $item->getImage();
                                if ($image instanceof Image) {
                                    $image->delete();
                                }
                            }
                        }
    
                        $items = [];
                        $hotspotImages = [];
    
                        foreach ($FloormapData as $file) {
                            $hotspotImage = new Hotspotimage();
                            // Check if the file is an instance of UploadedFile
                            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                                // Create a new Image instance and set it for Hotspotimage
                                $imageName = $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/FloorMaps"));
                                $image->save();
                                $hotspotImage->setImage($image);
                            }
    
                            $items[] = $hotspotImage;
                        }
    
                        $ProProject->setFloorMaps(new ImageGallery($items));    
                    }
                }
                // Gallery Handling
                if($galleryData){

                    $existingGallery = $ProProject->getProjectGallery();
                    // Delete old ImageGallery assets
                    if ($existingGallery instanceof ImageGallery) {
                        foreach ($existingGallery->getItems() as $item) {
                            $image = $item->getImage();
                            if ($image instanceof Image) {
                                $image->delete();
                            }
                        }
                    }

                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            // $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                            if ($customertype === 'Contractor'){
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProjectGallery"));
                                }
                            elseif ($customertype === 'Designer'){
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                                }
                            elseif ($customertype === 'Architect'){
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/ProjectGallery"));
                                }
                            elseif ($customertype === 'Builder'){
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/ProjectGallery"));
                                }
                            elseif ($customertype === 'Engineer'){
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/ProjectGallery"));
                                }
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                }
                $ProProject->setPublished(true);    
                $ProProject->save();
                $this->addFlash('success', $translator->trans('Project Updated succesfully.'));
            }
                

            return $this->render('Professional/dashboard_project_edit.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProProject' => $ProProject,
                    'form' => $form->createView(),
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * 
     *
     * @Route("/requirements/BOQ", name="Requirements-list")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function BoqListingAction(Request $request, Security $security, Translator $translator, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        

        $requirementList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
        $requirements = $requirementList->load();

        // Filter in PHP
        $Requirements = [];
        foreach ($requirements as $requirement) {
            if ($requirement->getExpiryCheck() === 'Active') {
                $Requirements[] = $requirement;
            }
        }

        // $form = $this->createForm(FilterFormType::class);
        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $formData = $form->getData();
        //     $FilterCity = $form->get('FilterCity')->getData();
        //     $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        //     $ProProfileList->addConditionParam("PortfolioType = ?", "Contractor");
        //     $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        //     $ProProfiles = $ProProfileList->load();
        // }

        // Paginate the ProProfiles
        $pagination = $paginator->paginate(
            $Requirements,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/requirementsListing.html.twig', [
            'Requirements' => $pagination,
            // 'filterform' => '0',
            // 'form' => $form->createView(),
            // 'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }


    /**
     * 
     *
     * @Route("/account/Requirements", name="account-Requirements")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardRequirementsAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProRequirementsList->setOrderKey('creationDate');
            $ProRequirementsList->setOrder('desc');

            $ProRequirements = $ProRequirementsList->load();

            $activeRequirements = [];
            $expiredRequirements = [];

            foreach ($ProRequirements as $ProRequirement) {
                if ($ProRequirement->getExpiryCheck() === 'Active') {
                    $activeRequirements[] = $ProRequirement;
                } elseif ($ProRequirement->getExpiryCheck() === 'Expired') {
                    $expiredRequirements[] = $ProRequirement;
                }
            }


            return $this->render('Professional/dashboard_Requirements_list.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProRequirements' => $ProRequirements,
                    'activeRequirements' => $activeRequirements,
                    'expiredRequirements' => $expiredRequirements,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * 
     *
     * @Route("/account/Proposals", name="account-Proposals")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardProposalsAction(Request $request, Security $security, Translator $translator, PaginatorInterface $paginator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProRequirementsList->setOrderKey('creationDate');
            $ProRequirementsList->setOrder('desc');

            $ProRequirements = $ProRequirementsList->load();

            $pagination = $paginator->paginate(
                $ProRequirements,
                $request->query->getInt('page', 1),
                10  // Number of items per page
            );
            $paginationVariables = $pagination->getPaginationData();


            return $this->render('Professional/dashboard_Proposal_list.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'Requirements' => $pagination,
                'paginationVariables' => $paginationVariables,
            
            ]);
        
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * @Route("/account/Add-Requirements", name="account-Add-Requirements")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardAddRequirementsAction(Request $request, Security $security, Translator $translator, LoggerInterface $logger)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(ProRequirementFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Handle file upload and save the ProRequirement object
                $uploadedFile = $form->get('excelFile')->getData();

                try {
                    // Other code for handling ProRequirement object...
                    $proRequirement = new ProRequirement();
                    $asset = new Document();
                    $asset->setData(file_get_contents($uploadedFile->getPathname()));
                    $timestamp = time();
                    $originalFilename = $uploadedFile->getClientOriginalName();
                    $newFilename = $timestamp . '_' . $originalFilename;
                    $asset->setFilename($newFilename);

                    // Map customertype to asset path
                    $assetPaths = [
                        'Contractor' => '/Services/Contractors/Requirements',
                        'Designer' => '/Services/Designers/Requirements',
                        'Architect' => '/Services/Architects/Requirements',
                        'Builder' => '/Services/Builders/Requirements',
                        'Engineer' => '/Services/Engineers/Requirements'
                    ];

                    if (array_key_exists($customertype, $assetPaths)) {
                        $asset->setParent(\Pimcore\Model\Asset::getByPath($assetPaths[$customertype]));
                    } else {
                        throw new \Exception('Unknown customertype: ' . $customertype);
                    }

                    $asset->save();
                    $proRequirement->setExcelFile($asset);
                    $objKey = $timestamp;
                    $proRequirement->setKey($objKey); // Use timestamp as the key
                    $proRequirement->setParent(Service::createFolderByPath('/Requirements'));
                    $proRequirement->setTitle($form->get('Title')->getData());
                    $proRequirement->setDescription($form->get('Description')->getData());
                    $proRequirement->setCity($form->get('City')->getData());
                    $proRequirement->setProfessional($ProProfile);
                    // $proRequirement->setProfessionalPath($ProProfile);
                    $excelData = $this->processExcelData($uploadedFile);
                    $proRequirement->setExcelData($excelData);
                    $TargetPrice = $form->get('TargetPrice')->getData();
                    if ($TargetPrice) {
                        $proRequirement->setTargetPrice($TargetPrice);
                    }

                    // Handle ExpireDate
                    $expireDate = $form->get('ExpireDate')->getData();
                    if ($expireDate) {
                        $expireDate = Carbon::instance($expireDate)->setTimezone('Asia/Kolkata');
                        $proRequirement->setExpireDate($expireDate);
                    }

                    $proRequirement->setPublished(true);
                    $proRequirement->setObjeKey($objKey);

                    $proRequirement->save();

                    // Create a directory named as the key of the proRequirement
                    $productFolderPath = '/Requirements/Products/' . $proRequirement->getKey();
                    Service::createFolderByPath($productFolderPath);

                    // Load the Excel file
                    $spreadsheet = IOFactory::load($uploadedFile->getPathname());
                    $worksheet = $spreadsheet->getActiveSheet();

                    // Loop through the rows and create ProRequirementProduct objects
                    foreach ($worksheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);

                        $rowData = [];
                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        // Skip header and empty rows
                        if ($rowData[0] == 'S.No' || empty($rowData[0])) {
                            continue;
                        }

                        // Check if the product name is not empty and does not contain "not included"
                        if (!empty($rowData[1]) && stripos($rowData[1], 'not included') === false) {
                            // Create ProRequirementProduct object
                            $proRequirementProduct = new ProRequirementProduct();
                            $proRequirementProduct->setProductName($rowData[1]);
                            $proRequirementProduct->setBrand($rowData[2]);
                            $proRequirementProduct->setMaterial($rowData[3]);
                            $proRequirementProduct->setMinDec($rowData[11]);
                            $proRequirementProduct->setQuantity($rowData[6]);
                            $proRequirementProduct->setUnit($rowData[7]);
                            $proRequirementProduct->setMinimumReserve($rowData[12]);
                            $proRequirementProduct->setDescription($rowData[5]);
                            $proRequirementProduct->setProRequirement($proRequirement); // Set the relation to ProRequirement
                            // Handle ExpireDate
                            $expireDate = $form->get('ExpireDate')->getData();
                            if ($expireDate) {
                                $expireDate = Carbon::instance($expireDate)->setTimezone('Asia/Kolkata');
                                $proRequirementProduct->setEndDate($expireDate);
                            }

                            // Save the ProRequirementProduct object in the folder named as the key of the proRequirement
                            $proRequirementProduct->setParent(Service::createFolderByPath($productFolderPath));
                            $proRequirementProduct->setKey(uniqid());
                            $proRequirementProduct->setPublished(true);
                            $proRequirementProduct->save();
                        }
                    }

                    // Redirect or do other actions
                    $this->addFlash('success', $translator->trans('Requirements submitted successfully.'));
                } catch (\Exception $e) {
                    // Handle file upload error
                    $this->addFlash('error', $translator->trans('An error occurred while submitting the requirements: ') . $e->getMessage());
                    $logger->error('Error while uploading requirements: ' . $e->getMessage());
                }
            }

            return $this->render('Professional/dashboard_Add_Requirements.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'form' => $form->createView()
            ]);
        }

        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }





    /**
     * 
     *
     * @Route("/account/Requirements/view/{url}", name="account-View-Requirement")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardViewRequirementAction($url, Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(ProRequirementEditFormType::class);

            if ($customertype === 'Contractor'){
                $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
                }
            elseif ($customertype === 'Designer'){
                
                $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
                }
            elseif ($customertype === 'Architect'){
                
                $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
                }
            elseif ($customertype === 'Builder'){
                $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
                }
            elseif ($customertype === 'Engineer'){
                $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
                }
            foreach ($form->all() as $formField) {
                $fieldName = $formField->getName();
        
                // Exclude ProfileImage field
                if ($fieldName !== 'excelFile' && $fieldName !== '_submit') {
                    $formField->setData($ProRequirement->{'get' . ucfirst($fieldName)}());
                }
            }
            
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Handle file upload and save the ProRequirement object

                $uploadedFile = $form->get('excelFile')->getData();
                
                // $ProRequirement->setKey(time());
                // $ProRequirement->setParent(Service::createFolderByPath('/Requirements'));
                $ProRequirement->setTitle($form->get('Title')->getData());
                
                $ProRequirement->setDescription($form->get('Description')->getData());
                $ProRequirement->setProfessional($ProProfile);
                $ProRequirement->setProfessionalPath($ProProfile);

                if($uploadedFile){

                    $existingExcelData = $proRequirement->getExcelData();
                    // Delete old Excel data assets
                    if ($existingExcelData instanceof Document) {
                        $existingExcelData->delete();
                    }
                    try {
                        $asset = new Document();
                        $asset->setData(file_get_contents($uploadedFile->getPathname()));
                        $timestamp = time();
                        $originalFilename = $uploadedFile->getClientOriginalName();
                        $newFilename = $timestamp . '_' . $originalFilename;
                        $asset->setFilename($newFilename); // Set the desired filename

                        // Save the asset in the "/Services/Requirements" directory
                        if ($customertype === 'Contractor'){
                            $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/Requirements"));
                            }
                        elseif ($customertype === 'Designer'){
                            $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/Requirements"));
                            }
                        elseif ($customertype === 'Architect'){
                            $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/Requirements"));
                            }
                        elseif ($customertype === 'Builder'){
                            $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/Requirements"));
                            }
                        elseif ($customertype === 'Engineer'){
                            $asset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/Requirements"));
                            }
                        
                        $asset->save();
                        $excelData = $this->processExcelData($uploadedFile);
                        $ProRequirement->setExcelFile($asset);
                        $ProRequirement->setExcelData($excelData);
                        
                    } catch (FileException $e) {
                        // Handle file upload error
                        // Log the error or show a flash message to the user
                    }
        
                }

                $ProRequirement->save();
                $this->addFlash('success', $translator->trans('Requirements submitted succesfully.'));
            }


            return $this->render('Professional/dashboard_View_Requirements.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProRequirement' => $ProRequirement,
                    'form' => $form->createview()
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    public function downloadExcelAction($filename)
    {
        $directoriesToSearch = [
            '/Services/Architects/Requirements/',
            '/Services/Builders/Requirements/',
            '/Services/Contractors/Requirements/',
            '/Services/Designers/Requirements/',
            // Add more directories as needed
        ];
    
        $asset = $this->findAssetInDirectories($filename, $directoriesToSearch);
        // $asset = Document::getByPath($assetPath);

        if ($asset instanceof Document) {
            // Retrieve the file data
            $fileData = $asset->getData();
    
            // Set appropriate headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // Set content type for Excel files
            header('Content-Disposition: attachment; filename="' . $asset->getFilename() . '"'); // Force download with original filename
            header('Content-Length: ' . strlen($fileData)); // Set content length for accurate download progress
    
            // Output the file data
            echo $fileData;
            exit(); // Prevent further execution
        } else {
            throw $this->createNotFoundException('The requested Excel file was not found.');
        }
    }

    private function findAssetInDirectories($filename, $directories)
    {
        foreach ($directories as $directory) {
            $assetPath = $directory . $filename;
            $asset = Document::getByPath($assetPath);

            if ($asset instanceof Document) {
                return $asset;
            }
        }

        return null;
    }


    /**
     * 
     *
     * @Route("/account/Enquiries", name="account-Enquiries")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardEnquiriesAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProEnquiryList = new \Pimcore\Model\DataObject\ProEnquiry\Listing();
            $ProEnquiryList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProEnquiryList->setOrderKey('creationDate');
            $ProEnquiryList->setOrder('desc');

            $ProEnquiries = $ProEnquiryList->load();


            return $this->render('Professional/dashboard_Enquiries_list.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProEnquiries' => $ProEnquiries,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * 
     *
     * @Route("/account/Endorsements", name="account-Endorsement")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardEndorsementAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerID = $customer->getUserID();
            //$ProProfile = $customer->getPortfolio();
            $form = $this->createForm(EndorsementRequestFormType::class);
            $form->handleRequest($request);
            $EndorsementFormURL = '/user/' . $customerID . '/endorsement/';
            $Endorsements = $customer->getEndorsement();
            $numberOfEndorsements = $customer->getEndorsements();



            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProEndorsementRequest= new ProEndorsementRequest();
                $ProEndorsementRequest->setParent(Service::createFolderByPath('/Endorsement/Endorsement'));
                $ProEndorsementRequest->setKey(Text::toUrl(time()));
                $ProEndorsementRequest->setName($form->get('Name')->getData());
                $ProEndorsementRequest->setEmail($form->get('Email')->getData());
                if ($customer->getPortfolioActivate() === 'true') {
                    $ProProfiles = $customer->getPortfolio();
                    $ProProfile = $ProProfiles[0];
                    $ProEndorsementRequest->setProfessional($ProProfile);
                }
                
                // Send Email
                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EndorsementRequest");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];
                
                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                eval("\$htmlContent = \"$htmlContent\";");
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($form->get('Email')->getData());
                $mail->subject($subject);

                $mail->html($htmlContent);
                $mail->send();

                // SEND Email Finish

                $ProEndorsementRequest->setPublished(true);    
                $ProEndorsementRequest->save();
                $this->addFlash('success', $translator->trans('Endorsement Request Sent Successfully!'));

            }


            return $this->render('Professional/dashboard_Endorsement.html.twig', [
                    'customer' => $customer,
                    'form' => $form->createView(),
                    'Endorsements' => $Endorsements,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * @Route("/htmlbuilder/demo/{url}", name="demopage")
     */
    public function htmlDemo($url)
    {
        // Load ArchitectProfile based on the URL
        $architectProfile = ArchitectProfile::getByPath("/Services/Architects/Profiles/$url");

        if (!$architectProfile) {
            throw $this->createNotFoundException('Architect profile not found');
        }

        $architectProjects = ArchitectProjects::getList([
            'architect' => $architectProfile->getId(),
            'unpublished' => false,
        ]);
        
        $selectedProjects = [];
        
        foreach ($architectProjects as $project) {
            if ($project->getArchitect() === $architectProfile) {
                $selectedProjects[] = $project;
            }
        }
        
        return $this->render('Demo/demo.html.twig', [
            'architectProfile' => $architectProfile,
            'projects' => $selectedProjects,
        ]);
    }

    //PROPROFILES CONTROLLER ACTIONS STATS HERE - Professional @Route("/portfolio/signup", name="Portfolio-Signup")
    
    /**
     * @Route("/professional-signup", name="Professional-Sign-up")
     */
     public function PortfolioSubmitStep1(Request $request, Security $security, Translator $translator, UserInterface $user = null)
     {
        return $this->render('Professional/signup-Portfolio-step1.html.twig', [
        ]);
     }


    /**
     * @param Request $request
     * @param Translator $translator
     * 
     * @return Response
     *
     * @throws \Exception
     */
    
    public function PortfolioSubmitAction(Request $request, Security $security, Translator $translator, UserInterface $user = null)
    {
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $security->getUser();
            $customertype = $customer->getcustomertype();
         
            if ($customertype == 'Contractor') {
                $form = $this->createForm(ContractorRegistrationFormType::class);
            }
            if ($customertype == 'Designer') {
                $form = $this->createForm(DesignerRegistrationFormType::class);
            }

            if ($customertype == 'Architect') {
                $form = $this->createForm(ArchitectRegistrationFormType::class);
            }
            if ($customertype == 'Builder') {
                $form = $this->createForm(BuilderRegistrationFormType::class);
            }
            if ($customertype == 'Manufacturer') {
                $form = $this->createForm(ManufacturerRegistrationFormType::class);
            }
            if ($customertype == 'Engineer') {
                $form = $this->createForm(EngineerRegistrationFormType::class);
            }
            if ($customertype == 'Distributor') {
                $form = $this->createForm(DistributorRegistrationFormType::class);
            }
            if ($customertype == 'Retailer') {
                $form = $this->createForm(RetailerRegistrationFormType::class);
            }

            $form->handleRequest($request);
            $PortfolioActivate = $customer->getPortfolioActivate();
            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProProfile = new ProProfile();
                if ($customertype == 'Contractor') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Contractors/Profiles'));
                }
                if ($customertype == 'Designer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Designers/Profiles'));
                }
                if ($customertype == 'Architect') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Architects/Profiles'));
                }
                if ($customertype == 'Builder') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Builders/Profiles'));
                }
                if ($customertype == 'Manufacturer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Manufacturers/Profiles'));
                }
                if ($customertype == 'Engineer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Engineers/Profiles'));
                }
                if ($customertype == 'Distributor') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Distributors/Profiles'));
                }
                if ($customertype == 'Retailer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Retailers/Profiles'));
                }
                $imageData = $form->get('ProfileImage')->getData();
                $ProProfile->setCustomer($customer);
                if ($imageData) {
                    $imageName = $imageData->getClientOriginalName();
                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                    $newAsset = new Image();
                    
                    $newAsset->setFilename($imageName);
                    if ($customertype == 'Contractor') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProfileGallery"));
                    }
                    if ($customertype == 'Designer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProfileGallery"));
                    }
                    if ($customertype == 'Architect') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Architects/ProfileGallery"));
                    }
                    if ($customertype == 'Builder') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/ProfileGallery"));
                    }
                    if ($customertype == 'Manufacturer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProfileGallery"));
                    }
                    if ($customertype == 'Engineer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/ProfileGallery"));
                    }
                    if ($customertype == 'Distributor') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProfileGallery"));
                    }
                    if ($customertype == 'Retailer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProfileGallery"));
                    }
                    $newAsset->setData(file_get_contents($imageData->getPathname()));
                    $newAsset->save();
                    $ProProfile->setProfileImage($newAsset);
                }
            
                $ProProfile->setKey(Text::toUrl($formData['CompanyName'] . '-' . time()));
                $ProProfile->setCompanyName($form->get('CompanyName')->getData());
                if ($customertype != 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer') {

                    if($form->has('Specialization') && $form->get('Specialization')->getData()){
                        $ProProfile->setspecialization($form->get('Specialization')->getData());
                    }

                    
                    if($form->has('Skills') && $form->get('Skills')->getData()){
                        $skillsData = $form->get('Skills')->getData();
                        if (is_array($skillsData)) {
                            // Skills submitted as Select2 (array), implode them
                            $skillsString = implode(',', $skillsData);
                        } else {
                            // Skills submitted as manually typed text, use as is
                            $skillsString = $skillsData;
                        }
                        $ProProfile->setSkills($skillsString);
                    }
                }
                if($form->has('gstnumber') && $form->get('gstnumber')->getData()){
                    $ProProfile->setgstnumber($form->get('gstnumber')->getData());
                }
                $ProProfile->setYearEstablished($form->get('YearEstablished')->getData());
                if ($customertype != 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer') {
                    if($form->has('YearOfCertification') && $form->get('YearOfCertification')->getData()){
                        $ProProfile->setYearOfCertification($form->get('YearOfCertification')->getData());
                    }
                    if ($customertype == 'Architect') {
                        if($form->has('CoaNumber') && $form->get('CoaNumber')->getData()){
                            $ProProfile->setCoaNumber($form->get('CoaNumber')->getData());
                        }
                    }
                }
                
                if($form->get('CitiesServed')->getData()){
                    $ProProfile->setCitiesServed(implode(',', $form->get('CitiesServed')->getData()));
                }
                $ProProfile->setDescription($form->get('Description')->getData());

                if ($customertype !== 'Builder' && $customertype !== 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer') {
                    if($form->get('PriceForHour')->getData()){
                        $ProProfile->setPriceForHour($form->get('PriceForHour')->getData());
                    }
                }

                

                $ProProfile->setCountryCode($form->get('CountryCode')->getData());
                $ProProfile->setPhoneNumber($form->get('PhoneNumber')->getData());
                $ProProfile->setStreetAddress($form->get('StreetAddress')->getData());
                $ProProfile->setCity($form->get('City')->getData());
                $ProProfile->setState($form->get('State')->getData());
                $ProProfile->setCountry($form->get('Country')->getData());
                $ProProfile->setPinCode($form->get('PinCode')->getData());
                $ProProfile->setPortfolioType($customertype);
                $ProProfile->setPublished(true);

                $ProProfile->save();
                


                $customer->setPortfolioActivate('true');
                // $customer->setPortfolio($ProProfile);
                $customer->save();
                
                $Notification = new ProNotification();
                $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
                $Notification->setKey(Text::toUrl(time()));
                $Notification->setMessage("Profile Details Submitted Successfully for Approval. You can sit back and relax!");
                $Notification->setDescription("You will be notified once the review is completed.");
                $Notification->setCustomer($customer);
                $redirecturl = 'javascript:void(0);';
                $Notification->seturl($redirecturl);
                $Notification->setPublished(true);
                $Notification->save();

                $this->addFlash('success', $translator->trans('Profile submitted succesfully.'));

                return $this->render('Professional/ProProfile_signup_success.html.twig', ['ProProfile' => $ProProfile, 'customertype' => $customertype]);
         
            }

            return $this->render('Professional/ProProfile_signup.html.twig', [
                'form' => $form->createView(),
                'customertype' => $customertype,
                'PortfolioActivate' => $PortfolioActivate
            ]);
            
        }

        return $this->render('Professional/NotLogged_signup.html.twig', [
             
        ]);
    }

    /**
     * @Route("/contractors/listing", name="Contractor-Listing")
     */
    public function ContractorlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Contractor");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Contractor");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        // Paginate the ProProfiles
        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'customertype' => 'Contractor',
            'filterform' => '0',
            'form' => $form->createView(),
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }


    /**
     * @Route("/architects/listing", name="Contractor-Listing")
     */
    public function ArchitectlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Architect");
        

        $ProProfiles = $ProProfileList->load();
        

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Architect");
            
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();


        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'form' => $form->createView(),
            'ProProfiles' => $pagination,
            'customertype' => 'Architect',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }


    /**
     * @Route("/designers/listing", name="Designers-Listing")
     */
    public function DesignerlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Designer");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Designer");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Designer',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/builders/listing", name="Builders-Listing")
     */
    public function BuilderlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Builder");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Builder");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Builder',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/manufacturers/listing", name="Manufacturers-Listing")
     */
    public function ManufacturerlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Manufacturer");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Manufacturer");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Manufacturer',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/distributors/listing", name="Distributors-Listing")
     */
    public function DistributorlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Distributor");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Distributor");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Distributor',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/retailers/listing", name="Retailer-Listing")
     */
    public function RetailerlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Retailer");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Retailer");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Retailer',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/engineers/listing", name="Engineers-Listing")
     */
    public function EngineerlistingAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Fetch ArchitectProfiles objects
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Engineer");

        $ProProfiles = $ProProfileList->load();

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $ProProfileList->addConditionParam("PortfolioType = ?", "Designer");
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
            $ProProfiles = $ProProfileList->load();
        }

        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            3  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render the template with the architect profiles
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Engineer',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
    * @Route("/portfolio/add-project", name="Professional-Add-Project")
    */
    public function ProProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            if ($customertype === 'Contractor') {
                $form = $this->createForm(ContractorAddProjectFormType::class);
            }
            if ($customertype === 'Designer') {
                $form = $this->createForm(DesignerAddProjectFormType::class);
            }
            if ($customertype === 'Architect') {
                $form = $this->createForm(ArchitectAddProjectFormType::class);
            }
            if ($customertype === 'Builder') {
                $form = $this->createForm(BuilderAddProjectFormType::class);
            }
            if ($customertype === 'Engineer') {
                $form = $this->createForm(EngineerAddProjectFormType::class);
            }
            

            
            $form->handleRequest($request);
            if ($customer->getPortfolioActivate() === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/'.$customertype.'s/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);
                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));   
                    
                    if ($form->has('FloorMaps') && $form->get('FloorMaps')->getData()){
                        $FloorMapData = $form->get('FloorMaps')->getData();
                        $Mapitems = [];
                        $maphotspotImages = [];

                        foreach ($FloorMapData as $file) {
                            $hotspotImage = new Hotspotimage();
                            // Check if the file is an instance of UploadedFile
                            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                                // Create a new Image instance and set it for Hotspotimage
                                $imageName = $file->getClientOriginalName();
                                $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/FloorMaps"));
                                $image->save();
                                $hotspotImage->setImage($image);
                            }

                            $Mapitems[] = $hotspotImage;
                        }
                        $ProProject->setFloorMaps(new ImageGallery($Mapitems));
                    }


                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
                }

                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    


    /**
    * @Route("/contractor/portfolio/add-project", name="Contractor-Add-Project")
    */
    public function ContractorProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(ContractorAddProjectFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Contractor' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Contractors/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
                }

                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }


    /**
    * @Route("/designer/portfolio/add-project", name="Designer-Add-Project")
    */
    public function DesignerProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(DesignerAddProjectFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Designer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Designers/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/architect/portfolio/add-project", name="Architect-Add-Project")
    */
    public function ArchitectProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(ArchitectAddProjectFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Architect' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Architects/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);
                    

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/builder/portfolio/add-project", name="Builder-Add-Project")
    */
    public function BuilderProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(BuilderAddProjectFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Builder' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Builders/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);
                    $ProProject->setReraApproval($form->get('ReraApproval')->getData());
                    $ProProject->setPossessionStarts($form->get('PossessionStarts')->getData());

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));
                    
                    
                    if ($form->get('FloorMaps')->getData()){
                        $FloorMapData = $form->get('FloorMaps')->getData();
                        $Mapitems = [];
                        $maphotspotImages = [];

                        foreach ($FloorMapData as $file) {
                            $hotspotImage = new Hotspotimage();
                            // Check if the file is an instance of UploadedFile
                            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                                // Create a new Image instance and set it for Hotspotimage
                                $imageName = $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/FloorMaps"));
                                $image->save();
                                $hotspotImage->setImage($image);
                            }

                            $Mapitems[] = $hotspotImage;
                        }
                        $ProProject->setFloorMaps(new ImageGallery($Mapitems));
                    }
                    

                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
                }

                
                return $this->render('Professional/builder_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/engineer/portfolio/add-project", name="Engineer-Add-Project")
    */
    public function EngineerProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(EngineerAddProjectFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Engineer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Engineers/Projects'));
        
                    $ProProject->setProfessional($ProProfile);
                    
                    $ProProject->setKey(Text::toUrl($formData['ProjectName'] . '-' . time()));
    
                    $ProProject->setProjectName($form->get('ProjectName')->getData());
                    $ProProject->setProjectDescription($form->get('ProjectDescription')->getData());
                    $ProProject->setLocation($form->get('Location')->getData());
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
    
                    return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype]);
                }

                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    
    /**
     * @Route("/designer/project/{url}", name="Designer_project_single")
     */
    public function DesignerProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load DesignerProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Designers/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Designers/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'designer',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/engineer/project/{url}", name="Engineer_project_single")
     */
    public function EngineerProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load DesignerProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Engineers/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Engineers/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'engineer',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/architect/project/{url}", name="Architect_project_single")
     */
    public function ArchitectProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Architects/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Architects/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());
            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'architect',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contractor/project/{url}", name="Contractor_project_single")
     */
    public function ContractorProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Contractors/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Contractors/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());
            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'contractor',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/builder/project/{url}", name="Builder_project_single")
     */
    public function BuilderProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Builders/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Builders/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'Builder',
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/contractor/portfolio/{url}", name="Contract_profile")
     */
    public function ContractorProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Contractors/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProjects= $ProProjectsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProjects,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProjects = count($selectedProjects);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Contractors/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/engineer/portfolio/{url}", name="Engineer_profile")
     */
    public function EngineerProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Engineers/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProjects= $ProProjectsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProjects,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProjects = count($selectedProjects);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Contractors/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }


     /**
     * @Route("/manufacturer/product/{url}", name="Manufacturer_product_single")
     */
    public function ManufacturerProductDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProduct = ProProduct::getByPath("/Services/Manufacturers/Products/$url");

        if (!$ProProduct) {
            throw $this->createNotFoundException('Product not found');
        }

        $ProProfile = $ProProduct->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Architects/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => 'manufacturer',
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/distributor/product/{url}", name="Distributor_product_single")
     */
    public function DistributorProductDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProduct = ProProduct::getByPath("/Services/Distributors/Products/$url");

        if (!$ProProduct) {
            throw $this->createNotFoundException('Product not found');
        }

        $ProProfile = $ProProduct->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Distributors/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => 'Distributor',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/retailer/product/{url}", name="retailer_product_single")
     */
    public function RetailerProductDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProduct = ProProduct::getByPath("/Services/Retailers/Products/$url");

        if (!$ProProduct) {
            throw $this->createNotFoundException('Product not found');
        }

        $ProProfile = $ProProduct->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Retailers/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/professional_project_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => 'Retailer',
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/designer/portfolio/{url}", name="designer_profile")
     */
    public function DesignerProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Designers/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProjects= $ProProjectsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProjects,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProjects = count($selectedProjects);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Designers/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/architect/portfolio/{url}", name="Architect_profile")
     */
    public function ArchitectsProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Architects/Profiles/$url");
        $customer = $ProProfile->getCustomer();
        $customertype = $customer->getcustomertype();

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProjects= $ProProjectsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProjects,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProjects = count($selectedProjects);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Architects/Enquiries'));
            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("You Got a New Customer Enquiry!");
            $Notification->setDescription("Click to Open Enquiry Details.");
            $Notification->setCustomer($customer);
            $redirecturl = '/enquiry/'.$customertype.'s/'.$ProEnquiry->getkey();
            // $Notification->setProEnquiry($ProEnquiry);
            $Notification->seturl($redirecturl);
            $Notification->setPublished(true);
            $Notification->save();
            

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/builder/portfolio/{url}", name="Builder_profile")
     */
    public function BuildersProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Builders/Profiles/$url");
        $customer = $ProProfile->getCustomer();
        $customertype = $customer->getcustomertype();

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
        $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProjects= $ProProjectsList->load();

        $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
        $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedRequirements= $ProRequirementsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProjects,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProjects = count($selectedProjects);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(BuilderEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Builders/Enquiries'));
            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            // $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("You Got a New Customer Enquiry!");
            $Notification->seturl("/enquiry/".$customertype."s/".$ProEnquiry->getKey());
            $Notification->setProfessional($ProProfile);
            $Notification->setCustomer($customer);
            
            $Notification->setProEnquiry($ProEnquiry);
            $Notification->setPublished(true);
            $Notification->save();

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/builder_profile.html.twig', [
            'BuilderProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
            'requirements' => $selectedRequirements,
        ]);
    }


    /**
     * @Route("/manufacturer/portfolio/{url}", name="Manufacturer_profile")
     */
    public function ManufacturersProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Manufacturers/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $ProProductsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProducts= $ProProductsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProducts,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProducts = count($selectedProducts);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Manufacturers/Enquiries'));
            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("You Got a New Customer Enquiry!");
            $Notification->setProfessional($ProProfile);
            $Notification->setProEnquiry($ProEnquiry);
            $Notification->setPublished(true);
            $Notification->save();

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProducts' => $numberOfProducts,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    
    /**
     * @Route("/distributor/portfolio/{url}", name="Distributor_profile")
     */
    public function DistributorProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Distributors/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $ProProductsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProducts= $ProProductsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProducts,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProducts = count($selectedProducts);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Distributors/Enquiries'));
            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("You Got a New Customer Enquiry!");
            $Notification->setProfessional($ProProfile);
            $Notification->setProEnquiry($ProEnquiry);
            $Notification->setPublished(true);
            $Notification->save();

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProducts' => $numberOfProducts,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }

    /**
     * @Route("/retailer/portfolio/{url}", name="Retailer_profile")
     */
    public function RetailerProfileAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProfile = ProProfile::getByPath("/Services/Retailers/Profiles/$url");

        if (!$ProProfile) {
            throw $this->createNotFoundException('profile not found');
        }

        $ProProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $ProProductsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

        $selectedProducts= $ProProductsList->load();
        
    
        $pagination = $paginator->paginate(
            $selectedProducts,
            $request->query->getInt('page', 1),
            2  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();
        $numberOfProducts = count($selectedProducts);

        $creationTimestamp = $ProProfile->getCreationDate();
        $creationDate = new \DateTime();
        $creationDate->setTimestamp($creationTimestamp);
        $formattedCreationDate = $creationDate->format('M Y');

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Retailers/Enquiries'));
            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $Notification->setKey(Text::toUrl(time()));
            $Notification->setMessage("You Got a New Customer Enquiry!");
            $Notification->setProfessional($ProProfile);
            $Notification->setProEnquiry($ProEnquiry);
            $Notification->setPublished(true);
            $Notification->save();

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProducts' => $numberOfProducts,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
        ]);
    }


    /**
     * @Route("/enquiry/{enquirytype}/{url}", name="Enquiry-page")
     */
    public function EnquiryPageAction($enquirytype, $url, Request $request, Security $security, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL

        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProEnquiry = ProEnquiry::getByPath("/Services/$enquirytype/Enquiries/$url");

            if (!$ProEnquiry) {
                throw $this->createNotFoundException('Invalid Enquiry URL');
            }
            $protype = $ProEnquiry->getProfessional();
            $unlock = $ProEnquiry->getUnlock();


            return $this->render('Professional/professional_enquiry.html.twig', [
                'ProEnquiry' => $ProEnquiry,
                'enquirytype' => $enquirytype,
                'url' => $url,
                'Unlock' => $unlock,
                'customer' => $customer,
             ]);
        // If the user is not an architect or the architect is not activated
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/enquiry/{enquirytype}/{url}/unlock", name="Enquiry-page-unlock")
     */
    public function EnquiryUnlockAction($enquirytype, $url, Request $request, Security $security, PaginatorInterface $paginator)
    {
        $user = $security->getUser();
        $ProEnquiry = ProEnquiry::getByPath("/Services/$enquirytype/Enquiries/$url");

        if (!$ProEnquiry) {
            throw $this->createNotFoundException('Invalid Enquiry URL');
        }

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $credits = $customer->getCreditPoints();
            $enquiryLink = '/enquiry/'.$enquirytype.'/'.$url;

            if ($credits >= 1) {

                $customer->setCreditPoints($credits - 1);
                $customer->save();
                $ProEnquiry->setUnlock('true');
                $ProEnquiry->save();
                return $this->render('Professional/dashboard_unlock_enquiry_success.html.twig', [
                    'enquiryLink' => $enquiryLink,
                ]);

            }else{
                return $this->render('Professional/dashboard_unlock_enquiry_failed.html.twig', [
                ]);
            }
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/account/pricing", name="Pricing-page")
     */
    public function PricingPageAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];  
            
        


            return $this->render('Professional/dashboard_pricing.html.twig', [
                'customer' => $customer,
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/create-order", name="create_order", methods={"POST"})
     */
    public function createOrder(Request $request, Security $security)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $plan = $request->request->get('plan');
        $amount = 0;

        switch ($plan) {
            case 'Monthly':
                $amount = 50000;
                break;
            case 'Quarterly':
                $amount = 150000;
                break;
            case 'HalfYearly':
                $amount = 300000;
                break;
            case 'Yearly':
                $amount = 600000;
                break;
            default:
                return $this->json(['error' => 'Invalid plan selected'], 400);
        }

        $razorpayKey = 'rzp_test_zEHFqlpuKKoqNF'; 
        $razorpaySecret = 'RwzuMElv63i5n70l1SztGaj4'; 
        $api = new Api($razorpayKey, $razorpaySecret);

        try {
            $orderData = [
                'amount' => $amount, // Amount in paisa
                'currency' => 'INR',
                'receipt' => 'order_' . time(),
                'payment_capture' => 1
            ];
            $razorpayOrder = $api->order->create($orderData);
            return $this->json([
                'orderId' => $razorpayOrder['id'],
                'amount' => $amount,
                'razorpayKey' => $razorpayKey,
                'name' => 'Arqonz Global Pvt. Ltd.',
                'description' => "Payment for $plan Plan",
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Razorpay order creation failed'], 500);
        }
    }




    /**
     * @Route("/subscribe/{plan}", name="subscribe")
     */
    public function subscribeAction(Request $request, Security $security, string $plan)
    {
        $user = $security->getUser();
       

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $razorpayKey = 'rzp_test_zEHFqlpuKKoqNF'; // Replace with your Razorpay key
            $razorpaySecret = 'RwzuMElv63i5n70l1SztGaj4'; // Replace with your Razorpay secret
            $api = new Api($razorpayKey, $razorpaySecret);

            $amount = 0;
            if ($plan === 'BasicPlan') {
                $amount = 100000.00;
            } elseif ($plan === 'EconomyPlan') {
                $amount = 200000.00;
            } elseif ($plan === 'AdvancedPlan') {
                $amount = 300000.00;
            }

            // Create a Razorpay order
            
            $orderData = [
                'amount' => $amount, // Amount in paisa
                'currency' => 'INR',
                'receipt' => 'order_' . time(),
                'payment_capture' => 1 // Auto capture
            ];
            
            $razorpayOrder = $api->order->create($orderData);
            $orderId = $razorpayOrder['id'];

            return $this->render('Professional/dashboard_checkout.html.twig', [
                'api' => $api,
                'plan' => $plan,
                'razorpayKey' => $razorpayKey,
                'orderid' => $orderId,
                'amount' => $amount,

            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/account/subscription/verify", name="Verifysubscription")
     */
    public function VerifySubsAction(Request $request, Security $security, PaginatorInterface $paginator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $razorpayKey = 'rzp_test_zEHFqlpuKKoqNF'; // Replace with your Razorpay key
            $razorpaySecret = 'RwzuMElv63i5n70l1SztGaj4'; // Replace with your Razorpay secret
            $api = new Api($razorpayKey, $razorpaySecret);

            // Check if payment is successful
            $paymentId = $request->query->get('payment_id');
            if ($paymentId) {
                try {
                    $attributes = array(
                        'razorpay_signature' => $request->query->get('razorpay_signature'),
                        'razorpay_payment_id' => $request->query->get('payment_id'),
                        'razorpay_order_id' => $request->query->get('order_id')
                    );

                    $api->utility->verifyPaymentSignature($attributes);
                    $timeZone = new \DateTimeZone('Asia/Kolkata');
                    $currentDate = new \DateTime('now', $timeZone);
                    $currentCarbonDate = Carbon::instance($currentDate);

                    // Payment successful, unlock details
                    $unlock = true;
                    $plan = $request->query->get('plan');
                    $PaymentOrder = new PaymentOrder();
                    $PaymentOrder->setParent(Service::createFolderByPath('/PaymentOrders'));
                    $PaymentOrder->setKey(uniqid());
                    $PaymentOrder->setProProfile($ProProfile);
                    if ($paymentId) {
                        $currentCreditsPoints = $customer->getCreditPoints();
                        if ($plan === 'Monthly') {
                            $customer->setCreditPoints($currentCreditsPoints + 5);
                            $customer->setSubscriptionPlan('Monthly');
                            $customer->setSubscriptionStart($currentCarbonDate);

                            $PaymentOrder->setSubscriptionPlan('Monthly');
                            $PaymentOrder->setOrderDate($currentCarbonDate);
                            $PaymentOrder -> save();


                        } elseif ($plan === 'Quarterly') {
                            $customer->setCreditPoints($currentCreditsPoints + 5);
                            $customer->setSubscriptionPlan('Quarterly');
                            $customer->setSubscriptionStart($currentCarbonDate);

                            $PaymentOrder->setSubscriptionPlan('Quarterly');
                            $PaymentOrder->setOrderDate($currentCarbonDate);
                            $PaymentOrder -> save();
                        } elseif ($plan === 'HalfYearly') {
                            $customer->setCreditPoints($currentCreditsPoints + 5);
                            $customer->setSubscriptionPlan('HalfYearly');
                            $customer->setSubscriptionStart($currentCarbonDate);

                            $PaymentOrder->setSubscriptionPlan('HalfYearly');
                            $PaymentOrder->setOrderDate($currentCarbonDate);
                            $PaymentOrder -> save();
                        } elseif ($plan === 'Yearly') {
                            $customer->setCreditPoints($currentCreditsPoints + 5);
                            $customer->setSubscriptionPlan('Yearly');
                            $customer->setSubscriptionStart($currentCarbonDate);

                            $PaymentOrder->setSubscriptionPlan('Yearly');
                            $PaymentOrder->setOrderDate($currentCarbonDate);
                            $PaymentOrder -> save();
                        }
                        $customer -> save();
                    }
                    
                } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                    // Handle signature verification error
                    $unlock = false;
                }
            } else {
                $unlock = false;
            }
            return $this->render('Professional/dashboard_subscription_verify.html.twig', [
            ]);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/requirements/{url}", name="Requirement")
     */
    public function RequirementDetailsAction($url, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
       
        // Load ArchitectProfile based on the URL
        $ProRequirement = ProRequirement::getByPath("/Requirements/$url");

        if (!$ProRequirement) {
            throw $this->createNotFoundException('Project not found');
        }

        //Fetch pro RequirementProducts
        $ProRequirementProducts = $ProRequirement->getProRequirementProduct();

        $ProProfile = $ProRequirement->getProfessional();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        $form1 = $this->createForm(ManufacturerRefferalFormtype::class);
        $form1->handleRequest($request);

        $form2 = $this->createForm(ProProposalFormType::class);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Architects/Enquiries'));

            $ProEnquiry->setProfessional($ProProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));

            $ProEnquiry->setfullname($form->get('fullname')->getData());
            $ProEnquiry->setEmail($form->get('Email')->getData());
            $ProEnquiry->setPhone($form->get('Phone')->getData());
            $ProEnquiry->setMessage($form->get('Message')->getData());

            if ($form->get('City')->getData()) {
                $ProEnquiry->setCity($form->get('City')->getData());
            }

            $ProEnquiry->setPublished(true);
    
            $ProEnquiry->save();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        if ($form1->isSubmitted()) {
            $form1Data = $form1->getData();
            $referredEmails = explode(',', $form1Data['Emails']);

            foreach ($referredEmails as $email) {
                // Send an email to each referred email address
                $this->sendReferralEmail($mailer, $email);
            }
            

            $ProRefferal = new ManufacturerRefferal();
            $ProRefferal->setParent(Service::createFolderByPath('/Refferals'));
            $ProRefferal->setKey(Text::toUrl(time()));
            $ProRefferal->setProfessional($ProProfile);
            $ProRefferal->setRequirement($ProRequirement);

            $ProRefferal->setEmails($form1->get('Emails')->getData());

            $ProRefferal->setPublished(true);
    
            $ProRefferal->save();
    
            $this->addFlash('success', 'Refferal Emails Sent.');
        }

        if ($form2->isSubmitted()) {
            $form2Data = $form2->getData();
            $uploadedFile = $form2->get('excelFile')->getData();
            $ProProposal = new ProProposal();


            try {
                $asset = new Document();
                $asset->setData(file_get_contents($uploadedFile->getPathname()));
                $timestamp = time();
                $originalFilename = $uploadedFile->getClientOriginalName();
                $newFilename = $timestamp . '_' . $originalFilename;
                $asset->setFilename($newFilename); // Set the desired filename
                $asset->setParent(\Pimcore\Model\Asset::getByPath("/Proposals"));
                
                $asset->save();
                $ProProposal->setExcelFile($asset);
                $ProProposal->setKey(time());
                $ProProposal->setParent(Service::createFolderByPath('/Proposals'));
                $ProProposal->setTitle($form2->get('Title')->getData());
                
                $ProProposal->setDescription($form2->get('Description')->getData());
                $ProProposal->setProfessional($ProProfile);
                $ProProposal->setProfessionalPath($ProProfile);
                $excelData = $this->processExcelData($uploadedFile);
                $ProProposal->setExcelData($excelData);
                $ProProposal->setRequirement($ProRequirement);

                $ProProposal->setPublished(true);

                $ProProposal->save();
                $customer = $ProProfile->getCustomer();

                $Notification = new ProNotification();
                $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
                $Notification->setKey(Text::toUrl(time()));
                $Notification->setMessage("You Recieved a New Proposal!");
                $Notification->setCustomer($customer);

                $Notification->seturl('/proposal/'.$ProProposal->getKey());
                $Notification->setPublished(true);
                $Notification->save();

                // Redirect or do other actions
                $this->addFlash('success', 'Proposal submitted succesfully.');
                
            } catch (FileException $e) {
                // Handle file upload error
                // Log the error or show a flash message to the user
            }
        }
        

        return $this->render('Professional/Professional_Requirement_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProRequirement,
            'ProRequirementProducts' => $ProRequirementProducts,
            'form' => $form->createView(),
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
        ]);
    }

    

    private function sendReferralEmail(MailerInterface $mailer, $recipientEmail)
    {
        $email = (new Email())
            ->from('Refferal@Arqonz.com')
            ->to($recipientEmail)
            ->subject('You have been referred!')
            ->html('<p>Dear user, you have been referred to check the Requirements BOQ. Please visit the link to view the details.</p>');

        $mailer->send($email);
    }

    /**
     * @Route("/proposal/{url}", name="Proposal")
     */
    public function ProposalPageAction($url, Request $request, PaginatorInterface $paginator)
    {
        // Load ArchitectProfile based on the URL
        $ProProposal = ProProposal::getByPath("/Proposals/$url");
        
        if (!$ProProposal) {
            throw $this->createNotFoundException('Invalid Enquiry URL');
        }
        $protype = $ProProposal->getProfessional();

        return $this->render('Professional/professional_proposal.html.twig', [
            'ProProject' => $ProProposal,
        ]);
    }


    /**
     * @Route("/proposals/{url}", name="Requirement-proposals")
     */
    public function RequirementproposalsAction($url, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
       
        // Load ArchitectProfile based on the URL
        $ProRequirement = ProRequirement::getByPath("/Requirements/$url");
        

        if (!$ProRequirement) {
            throw $this->createNotFoundException('Project not found');
        }

        //Fetch pro RequirementProducts
        $ProRequirementProducts = $ProRequirement->getProRequirementProduct();

        $ProProfile = $ProRequirement->getProfessional();
        $ProProposals = $ProRequirement->getProProposalBid();

        $pagination = $paginator->paginate(
            $ProProposals,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();


        

        return $this->render('Professional/Professional_Requirement-Proposals_single.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProposals' => $ProProposals,
            'paginationVariables' => $paginationVariables,
            
        ]);
    }



    /**
    * @Route("/manufacturer/add-product", name="Manufacturer-add-product")
    */
    public function ProductSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(AddProductFormType::class);
            $form->handleRequest($request);
            if ($customertype === 'Manufacturer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct = new ProProduct();
                    $ProProduct->setParent(Service::createFolderByPath('/Services/Manufacturers/Products'));
        
                    $ProProduct->setProfessional($ProProfile);
                    
                    $ProProduct->setKey(Text::toUrl($formData['ProductName'] . '-' . time()));
    
                    $ProProduct->setProductName($form->get('ProductName')->getData());
                    $ProProduct->setProductDescription($form->get('ProductDescription')->getData());
                    $ProProduct->setSpecifications($form->get('Specifications')->getData());
                    $ProProduct->setProductBrand($form->get('ProductBrand')->getData());
                    $ProProduct->setMaterial($form->get('ProductMaterial')->getData());
                    $ProProduct->setPrice($form->get('ProductPrice')->getData());
                    $ProProduct->setUnit($form->get('ProductUnit')->getData());
                    $ProProduct->setTags($form->get('ProductTags')->getData());

                    $ProProduct->setParentCategory($form->get('ParentCategory')->getData());
                    $ProProduct->setSubCategory($form->get('SubCategory')->getData());
                    $ProProduct->setSubSubCategory($form->get('SubSubCategory')->getData());
                    // $ProProduct->setProductCategories($form->get('categories')->getData());

                    $imageData = $form->get('ProductImage')->getData();

                    if ($imageData) {

                        // $previousimage = $ProProduct->getProductImage();
                        // $previousimage->delete();

                        $imageName = $imageData->getClientOriginalName();
                        $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                        $newAsset = new Image();
                        
                        $newAsset->setFilename($imageName);
                        
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                        
                        $newAsset->setData(file_get_contents($imageData->getPathname()));
                        $newAsset->save();
                        $ProProduct->setProductImage($newAsset);
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }


    // DEMO CONTROLLER FOR PRODUCT CATEGORY TESTING

    /**
    * @Route("/account/Product/edit/{url}", name="Dashboard-Product-Edit")
    */
    public function DashboardProductEditAction($url, Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
        $ProProduct = ProProduct::getByPath("/Services/Manufacturers/Products/$url");
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(AddProductFormType::class);
            foreach ($form->all() as $formField) {
                $fieldName = $formField->getName();
        
                // Exclude ProfileImage field
                if ($fieldName !== 'ProductImage' && $fieldName !== '_submit') {
                    $formField->setData($ProProduct->{'get' . ucfirst($fieldName)}());
                }

            }
            $form->handleRequest($request);
            
            if ($customertype === 'Manufacturer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct->setProductName($form->get('ProductName')->getData());
                    $ProProduct->setProductDescription($form->get('ProductDescription')->getData());
                    $ProProduct->setSpecifications($form->get('Specifications')->getData());

                    $ProProduct->setParentCategory($form->get('ParentCategory')->getData());
                    $ProProduct->setSubCategory($form->get('SubCategory')->getData());
                    $ProProduct->setSubSubCategory($form->get('SubSubCategory')->getData());

                    
                    $imageData = $form->get('ProductImage')->getData();

                    if ($imageData) {

                        $previousimage = $ProProduct->getProductImage();
                        $previousimage->delete();

                        $imageName = $imageData->getClientOriginalName();
                        $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                        $newAsset = new Image();
                        
                        $newAsset->setFilename($imageName);
                        
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                        
                        $newAsset->setData(file_get_contents($imageData->getPathname()));
                        $newAsset->save();
                        $ProProduct->setProductImage($newAsset);
                    }  
                       
                    $ProProduct->save();
    
    
                    $this->addFlash('success', $translator->trans('Product Updated succesfully.'));
    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/user/{userid}/endorsement", name="Professional-Endorsement")
     */
    public function ProfessionalEndorsementAction($userid, Request $request, PaginatorInterface $paginator)
    {   
        // Load ArchitectProfile based on the URL
        $CustomerList = new \Pimcore\Model\DataObject\Customer\Listing();
        $CustomerList->addConditionParam("UserID = ?", $userid);
        $Customers = $CustomerList->load();
        $customer = $Customers[0];
        //dump($customer);
        

        if ($customer->getPortfolioActivate() === 'false'){
            $firstName = $customer->getfirstname();
            $capitalizedFirstName = ucfirst($firstName);
        }else{
            $ProProfile =  $customer->getPortfolio()[0];
            $capitalizedFirstName = $customer->getfirstname();
        }


        $q1Value = '';
        $q2Value = '';
        
        $form = $this->createForm(ProEndorsementFormType::class, null, [
            'company_name' => $capitalizedFirstName,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEndorsement = new ProEndorsement();
            $ProEndorsement->setParent(Service::createFolderByPath('/Endorsements/Professionals/'));

            // $ProEndorsement->setProfessional($ProProfile);
            $ProEndorsement->setCustomer($customer);
            $ProEndorsement->setKey(Text::toUrl(time()));

            $ProEndorsement->setName($form->get('Name')->getData());
            $ProEndorsement->setEmail($form->get('Email')->getData());
            $ProEndorsement->setPhone($form->get('Phone')->getData());
            //$ProEndorsement->setProfession($form->get('Profession')->getData());
            $q1Text = $q1Value === 'Very Good' ? 'Very Good' : ($q1Value === 'Good' ? 'Good' : ($q1Value === 'Average' ? 'Average' : 'Poor'));
            $q2Text = $q2Value === 'Yes' ? 'Yes' : ($q2Value === 'No' ? 'No' : 'Maybe');

            // Create the desired string
            $Answers = sprintf('How Would you rate the Service Provided by: %s' . PHP_EOL . 'Would you Recommend this professionals to Others?: %s', $q1Text, $q2Text);
            
            $ProEndorsement->setAnswers($Answers);

            $ProEndorsement->setPublished(true);
    
            $ProEndorsement->save();

            $endorsementpoints = $customer->getEndorsements();
            $customer->setEndorsements($endorsementpoints + 1);
            $customer->save();

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EndorsementSuccessEmail");
            $EmailTemplates = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplates[0];
            
            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            eval("\$htmlContent = \"$htmlContent\";");
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($form->get('Email')->getData());
            $mail->subject($subject);

            $mail->html($htmlContent);
            $mail->send();

            // SEND Email Finish
            
            $this->addFlash('success', 'Endorsement submitted succesfully.');
        }

        return $this->render('Professional/professional_Endorsement.html.twig', [
            'form' => $form->createView(),
            'name' => $capitalizedFirstName,
        ]);
    }

    


} //end Bracket