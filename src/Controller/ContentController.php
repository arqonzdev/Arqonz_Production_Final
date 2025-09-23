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
use Pimcore\Model\DataObject\ContactForm;
use Pimcore\Model\DataObject\InvoiceCounter;
use Pimcore\Model\DataObject\BuilderEnquiry;
use Pimcore\Model\DataObject\ProEnquiry;
use Pimcore\Model\DataObject\ProNotification;
use Pimcore\Model\DataObject\ProReview;
use Pimcore\Model\DataObject\Customer;
use Pimcore\Model\DataObject\ProRequirementProduct;
use Pimcore\Model\DataObject\ProProposalBid;
use Pimcore\Model\DataObject\SupplierBid;
use Pimcore\Model\DataObject\ProductEnquiry;
use Pimcore\Model\DataObject\PaymentOrder;
use Pimcore\Model\DataObject\DbProductReview;
use Pimcore\Model\DataObject\AqThread;
use Pimcore\Model\DataObject\AqMessage;
use Pimcore\Model\DataObject\ProContractorVehicle;
use Pimcore\Model\DataObject\GlobalAwards;
use Pimcore\Model\DataObject\CustomerSurvey;
use Pimcore\Model\DataObject\CustomerSurveyAnswer;
use Parsedown;
//use App\Model\Customer;
use Pimcore\Model\DataObject\ProRequirement;
use Pimcore\Model\DataObject\DealDeskBuilder;
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
use App\Form\ProfessionalAddProjectFormType;
use App\Form\ContractorAddProjectFormType;
use App\Form\CombinedVerificationFormType;
use App\Form\ProductsPageFilterFormType;
use App\Form\DesignerAddProjectFormType;
use App\Form\ArchitectEnquiryFormType;
use App\Form\BuilderEnquiryFormType;
use App\Form\GetListedFormType;
use App\Form\BuilderRegistrationFormType;
use App\Form\ProRequirementFormType;
use App\Form\ProEnquiryFormType;
use App\Form\EmailVerificationFormtype;
use App\Form\GlobalAwardsFormType;
use App\Form\PhoneverifyFormType;
use App\Form\Profile_Edit_FormType;
use App\Form\EngineerRegistrationFormType;
use App\Form\RetailerRegistrationFormType;
use App\Form\FilterFormType;
use App\Form\BuilderProfileUpdateFormType;
use App\Form\ArchitectEditProjectFormType;
use App\Form\DesignerEditProjectFormType;
use App\Form\SupplierRegistrationFormType;
use App\Form\ContractorEditProjectFormType;
use App\Form\BuilderEditProjectFormType;
use App\Form\ProRequirementEditFormType;
use App\Form\ManufacturerRefferalFormtype;
use App\Form\ProProposalFormType;
use App\Form\BulkProjectsFormType;

use App\Form\BulkProductsFormType;
use App\Form\ManufacturerRegistrationFormType;
use App\Form\DealerRegistrationFormType;
use App\Form\DistributorRegistrationFormType;
use App\Form\AddVehicleFormType;
use App\Form\ProEndorsementFormType;
use App\Form\AddProductFormType;
use App\Form\SearchFormType;
use App\Form\NewsLetterFormType;
use App\Form\EndorsementRequestFormType;
use App\Form\EndorsementRequestFormTypeTEST;
use App\Form\EngineerAddProjectFormType;
use App\Form\EndorsementType;
use App\Form\AddProductFormTESTtype;
use App\Form\ProfessionalRegistrationFormType;

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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Client;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use \PDO;


use Symfony\Component\HttpFoundation\JsonResponse;

class ContentController extends BaseController
{
    
    public function defaultAction(Request $request, Translator $translator, LoggerInterface $logger)
    {
        return $this->render('Professional/404-error.html.twig', [
        ]);
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
    public function portalAction(Request $request, Translator $translator, LoggerInterface $logger)
    {   
        $ArchitectProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ArchitectProfilesList->addConditionParam("PortfolioType = ?", "Architect");
        $Architects = $ArchitectProfilesList->load();
        $Architects = array_slice($Architects, 0, 2);

        $metaTitle = "AI-Powered Construction Deals. No Delays.Just Decisions"; 
        $metaDescription = "Arqonz is a digital construction procurement platform that connects contractors, suppliers, and service providers on one unified interface. It simplifies vendor onboarding, provides live material tracking, and streamlines the ordering process for businesses.";

        $DesignerProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $DesignerProfilesList->addConditionParam("PortfolioType = ?", "Designer");
        $Designers = $DesignerProfilesList->load();
        $Designers = array_slice($Designers, 0, 2);

        $ContractorProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ContractorProfilesList->addConditionParam("PortfolioType = ?", "Contractor");
        $Contractors = $ContractorProfilesList->load();
        $Contractors = array_slice($Contractors, 0, 2);
        

        $BuilderProfilesList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $BuilderProfilesList->addConditionParam("PortfolioType = ?", "Builder");
        $Builders = $BuilderProfilesList->load();
        
        $Builders = array_slice($Builders, 0, 2);
        

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
            'contractors' => $Contractors,
            'builders' => $Builders,
            'metadescription' => $metaDescription,
            'metatitle' => $metaTitle,

        ]);
    }

    /**
     * @Route("/product-enquiry", name="product-enquiry", methods={"POST"})
     */
    public function handleProductEnquiry(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Retrieve the submitted form data
            $fullName = $request->request->get('fullname');
            $phoneNumber = $request->request->get('phonenumber');
            $emailAddress = $request->request->get('emailaddress');
            $productUrl = $request->request->get('productUrl');

            // Log the received data (optional)
            $logger->info('Received product enquiry', [
                'fullname' => $fullName,
                'phonenumber' => $phoneNumber,
                'emailaddress' => $emailAddress,
            ]);

            // Create a new ProductEnquiry object
            $productEnquiry = new ProductEnquiry();
            $productEnquiry->setKey(\Pimcore\File::getValidFilename($fullName . '-' . time()));
            $productEnquiry->setParent(\Pimcore\Model\DataObject::getByPath('/ProductEnquiry'));
            $productEnquiry->setFullName($fullName);
            $productEnquiry->setPhone($phoneNumber);
            $productEnquiry->setEmailAddress($emailAddress);
            $productEnquiry->setProductUrl($productUrl);
            $productEnquiry->setPublished(true);

            // Save the object
            $productEnquiry->save();

            // Return success response
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $logger->error('Error handling product enquiry: ' . $e->getMessage());

            // Return failure response
            return new JsonResponse(['success' => false], 500);
        }
    }


    /**
     * @Route("/product-review-submit", name="product_review_submit", methods={"POST"})
     */
    public function submitProductReview(Request $request, Security $security)
    {
        // Get the logged-in user
        $user = $security->getUser();

        // Retrieve the form data
        $rating = $request->request->get('rating');
        $review = $request->request->get('review');
        $productUrl = $request->request->get('productUrl');

        // Get the product ID from URL
        $productId = $this->getProductIdFromUrl($productUrl);

        // Determine if this is a ProProduct or db product based on URL structure
        $parsedUrl = parse_url($productUrl, PHP_URL_PATH);
        $segments = explode('/', trim($parsedUrl, '/'));
        
        // Check if this is a ProProduct URL (e.g., /manufacturer/product/product-key)
        $isProProduct = false;
        $proProduct = null;
        if (count($segments) >= 3) {
            $customerType = $segments[0];
            $productSegment = $segments[1];
            
            // Check if it's a ProProduct URL pattern
            if (in_array($customerType, ['manufacturer', 'dealer', 'distributor', 'supplier', 'retailer']) && 
                $productSegment === 'product') {
                $isProProduct = true;
                
                // Get the ProProduct object
                $proProductPath = "/Services/" . ucfirst($customerType) . "s/Products/$productId";
                $proProduct = \Pimcore\Model\DataObject\ProProduct::getByPath($proProductPath);
                
                // If not found in the specific customer type, try Suppliers as fallback
                if (!$proProduct) {
                    $proProductPath = "/Services/Suppliers/Products/$productId";
                    $proProduct = \Pimcore\Model\DataObject\ProProduct::getByPath($proProductPath);
                }
            }
        }

        if ($productId) {
            // Create the new ProductReview object
            $dbProductReview = new DbProductReview();
            $dbProductReview->setKey(\Pimcore\File::getValidFilename($productId . '-' . time()));
            $dbProductReview->setParent(\Pimcore\Model\DataObject::getByPath('/ProductReviews'));
            $dbProductReview->setRating($rating);
            $dbProductReview->setReview($review);
            $dbProductReview->setUniqueId($productId); 
            
            // Set the customer - the user object should be the Customer data object
            if ($user instanceof \Pimcore\Model\DataObject\Customer) {
                $dbProductReview->setCustomer($user);
            }
            
            // Set the ProProduct if this is a ProProduct review
            if ($isProProduct && $proProduct) {
                $dbProductReview->setProProduct($proProduct);
            }
            
            $dbProductReview->setPublished(true);
            // Save the object
            $dbProductReview->save();

            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Product not found']);
        }
    }

    /**
     * @Route("/portfolio-review-submit", name="portfolio_review_submit", methods={"POST"})
     */
    public function submitPortfolioReview(Request $request, Security $security)
    {   
        $user = $security->getUser();

        // Retrieve the form data
        $rating = $request->request->get('rating');
        $review = $request->request->get('review');
        $ProfileURL = $request->request->get('productUrl');

        $rating = (int) $rating;

        // Assuming the product ID is passed as a hidden input or can be retrieved from the URL
        $productId = $this->getProductIdFromUrl($ProfileURL);

        // Extract profession type and object key from the URL
        $parsedUrl = parse_url($ProfileURL, PHP_URL_PATH);
        $segments = explode('/', trim($parsedUrl, '/'));

        $professionType = ucfirst($segments[0]) . 's'; // e.g., 'Architect' => 'Architects'
        $objectKey = $segments[2]; // e.g., 'Madras-Makeovers-1710239976'
        
        $objectPath = "/Services/{$professionType}/Profiles/{$objectKey}";
        

        // Fetch the ProProfile object
        
        $proProfile = \Pimcore\Model\DataObject\ProProfile::getByPath($objectPath);

        // Fetch the product object

        

        if ($productId) {
            // Create the new ProductReview object
            $dbProductReview = new ProReview();
            $dbProductReview->setKey(\Pimcore\File::getValidFilename($productId . '-' . time()));
            $dbProductReview->setParent(\Pimcore\Model\DataObject::getByPath('/PortfolioReview'));
            $dbProductReview->setRating($rating);
            $dbProductReview->setReview($review);
            $dbProductReview->setReviewer($user);
            $dbProductReview->setUniqueId($productId); 
            $dbProductReview->setProfessional($proProfile);

            $dbProductReview->setPublished(true);    
            // Save the object
            $dbProductReview->save();

            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Product not found']);
        }
    }

    private function getProductIdFromUrl($url)
    {
        // Logic to extract the product ID from the URL
        // This is just an example and should be modified to suit your needs
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'];
        // Assuming the product ID is in the URL path, like /product/123
        $segments = explode('/', trim($path, '/'));
        return end($segments);
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

        return $this->render('Professional/home-demo-hero-new.html.twig', [
            'isPortal' => true,
            'form' => $form->createView(),
            'form1' => $form1->createView(), 
            'architects' => $Architects,
            'designers' => $Designers,
            'projects' => $BuilderProjects,

        ]);
    }




    /**
     * Helper method to get product image path from gallery
     */
    private function getProductImagePath($product, $thumbnail = 'product_listing')
    {
        try {
            $gallery = $product->getProductImage();
            
            // Handle ImageGallery
            if ($gallery instanceof ImageGallery && $gallery->getItems()) {
                $firstItem = $gallery->getItems()[0];
                if ($firstItem instanceof Hotspotimage) {
                    $image = $firstItem->getImage();
                    if ($image instanceof Image) {
                        if ($thumbnail) {
                            return $image->getThumbnail($thumbnail)->getPath();
                        } else {
                            return $image->getFullPath();
                        }
                    }
                }
            }
            
            // Handle direct Image (fallback for old data)
            if ($gallery instanceof Image) {
                if ($thumbnail) {
                    return $gallery->getThumbnail($thumbnail)->getPath();
                } else {
                    return $gallery->getFullPath();
                }
            }
            
        } catch (\Exception $e) {
            // Log error and return empty string
            error_log('Error getting product image path: ' . $e->getMessage());
        }
        
        return '';
    }

    /**
     * @Route("/products", name="Products-List")
     * @Route("/products/load-more", name="Products-List-Load-More")
     */
    public function ProductsListAction(Request $request, Security $security, SessionInterface $session, LoggerInterface $logger)
    {   
        // Define default values
        $defaultMin = 0;
        $defaultMax = 10000000;
        $defaultSort = 'default';

        $metaTitle = "Construction Materials Online | Cement, Tiles, Steel Bars & More | ArQonZ"; 
        $metaDescription = "Explore a wide range of high-quality construction materials on ArQonZ. From cement and TMT steel bars to tiles, plumbing, and electricals, find everything you need to build your dream project.";

        $minPrice = $session->get('minPrice', $defaultMin);
        $maxPrice = $session->get('maxPrice', $defaultMax);
        $sortOption = $session->get('sortOption', $defaultSort);
        
        $form = $this->createForm(ProductsPageFilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $minPrice = $formData['min'] ?? $defaultMin;
            $maxPrice = $formData['max'] ?? $defaultMax;
            $sortOption = $formData['sort'] ?? $defaultSort;

            $session->set('minPrice', $minPrice);
            $session->set('maxPrice', $maxPrice);
            $session->set('sortOption', $sortOption);
        }
        
        // Load and prepare ProProducts
        $proProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $proProducts = $proProductsList->load();
        
        // Filter ProProducts by price range
        $proProducts = array_filter($proProducts, function($product) use ($minPrice, $maxPrice) {
            $price = $product->getPrice();
            return $price >= $minPrice && $price <= $maxPrice;
        });
        
        // Sort ProProducts
        switch ($sortOption) {
            case 'priceLowHigh':
                usort($proProducts, function($a, $b) {
                    return $a->getPrice() <=> $b->getPrice();
                });
                break;
            case 'priceHighLow':
                usort($proProducts, function($a, $b) {
                    return $b->getPrice() <=> $a->getPrice();
                });
                break;
            default:
                break;
        }
        
        // Convert ProProducts to array format
        $proProductsArray = [];
        foreach ($proProducts as $proProduct) {
            $proProfile = $proProduct->getProfessional();
            $portfolioType = $proProfile ? strtolower($proProfile->getPortfolioType()) : '';
            $imagePath = $this->getProductImagePath($proProduct, null);
            
            $proProductsArray[] = [
                'Unique_ID' => $proProduct->getKey(),
                'Product_Name' => $proProduct->getProductName(),
                'Product_Price' => $proProduct->getPrice(),
                'Product_Unit' => $proProduct->getUnit() ?: 'unit',
                'Image_Path' => $imagePath,
                'isProProduct' => true,
                'viewLink' => '/' . $portfolioType . '/product/' . $proProduct->getKey()
            ];
        }

        // Database connection for scraped products
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // For AJAX requests (load more)
        if ($request->isXmlHttpRequest() || $request->get('_route') === 'Products-List-Load-More') {
            $offset = $request->query->get('offset', 0);
            $limit = 10;
            
            $sql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                        MIN(pc.Product_Price) as Product_Price, 
                        MIN(pc.Product_Unit) as Product_Unit,
                        MIN(pi.product_image_Path) as Image_Path
                    FROM products p
                    LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE pc.Product_Price BETWEEN :minPrice AND :maxPrice
                    GROUP BY p.ID, p.Unique_ID, p.Product_Name";
            
            switch ($sortOption) {
                case 'priceLowHigh':
                    $sql .= " ORDER BY MIN(pc.Product_Price) ASC";
                    break;
                case 'priceHighLow':
                    $sql .= " ORDER BY MIN(pc.Product_Price) DESC";
                    break;
                default:
                    $sql .= " ORDER BY p.ID ASC";
                    break;
            }
            
            $sql .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $stmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            $scrapedProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($scrapedProducts as &$product) {
                if ($product['Image_Path']) {
                    $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
                }
                $product['isProProduct'] = false;
                $product['viewLink'] = '/products/' . $product['Unique_ID'];
            }
            
            // For first load, merge ProProducts with scraped products
            if ($offset === 0) {
                $allProducts = array_merge($proProductsArray, $scrapedProducts);
            } else {
                $allProducts = $scrapedProducts;
            }
            
            $html = $this->renderView('Professional/product_items.html.twig', [
                'products' => $allProducts
            ]);
            
            $countSql = "SELECT COUNT(DISTINCT p.ID) as total 
                        FROM products p
                        LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                        WHERE pc.Product_Price BETWEEN :minPrice AND :maxPrice";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $countStmt->execute();
            $total = $countStmt->fetchColumn();
            
            return new JsonResponse([
                'html' => $html,
                'hasMore' => ($offset + $limit) < $total
            ]);
        }

        // For initial page load
        $initialSql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                            MIN(pc.Product_Price) as Product_Price, 
                            MIN(pc.Product_Unit) as Product_Unit,
                            MIN(pi.product_image_Path) as Image_Path
                    FROM products p
                    LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE pc.Product_Price BETWEEN :minPrice AND :maxPrice
                    GROUP BY p.ID, p.Unique_ID, p.Product_Name";
        
        switch ($sortOption) {
            case 'priceLowHigh':
                $initialSql .= " ORDER BY MIN(pc.Product_Price) ASC";
                break;
            case 'priceHighLow':
                $initialSql .= " ORDER BY MIN(pc.Product_Price) DESC";
                break;
            default:
                $initialSql .= " ORDER BY p.ID ASC";
                break;
        }
        
        $initialSql .= " LIMIT 10";
        
        $initialStmt = $pdo->prepare($initialSql);
        $initialStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
        $initialStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
        $initialStmt->execute();
        
        $initialScrapedProducts = $initialStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($initialScrapedProducts as &$product) {
            if ($product['Image_Path']) {
                $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
            }
            $product['isProProduct'] = false;
            $product['viewLink'] = '/products/' . $product['Unique_ID'];
        }

        // Merge ProProducts and scraped products for initial load
        $allProducts = array_merge($proProductsArray, $initialScrapedProducts);

        // Get total count for initial load
        $countSql = "SELECT COUNT(DISTINCT p.ID) as total 
                    FROM products p
                    LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    WHERE pc.Product_Price BETWEEN :minPrice AND :maxPrice";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
        $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
        $countStmt->execute();
        $totalProducts = $countStmt->fetchColumn();
        
        // Get categories
        $Categoriessql = "SELECT Category_Id, Category_Name, Category_Slug, Category_Image_Path FROM Product_Categories";
        $Categoriesstmt = $pdo->prepare($Categoriessql);
        $Categoriesstmt->execute();
        $Categories = $Categoriesstmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('Professional/products_list.html.twig', [
            'form' => $form->createView(),
            'products' => $allProducts,
            'totalProducts' => $totalProducts + count($proProductsArray),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
            'Categories' => $Categories,
            'metadescription' => $metaDescription,
            'metatitle' => $metaTitle,
        ]);
    }
        
    
    


   
    /**
     * @Route("/products/{url}", name="Products_Single_Page")
     */
    public function productsSinglePageAction($url, Security $security, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {   
        

        $keyword = $url;

        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT ID, Product_Name, Product_Brand, Product_Type, Product_Description, Product_Category, Product_SubCategory, Product_Sub_SubCategory, Specification_1 FROM products WHERE Unique_ID = :UniqueId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':UniqueId', $keyword);
        $stmt->execute();
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        $productId = $product['ID'];
        $specifications = json_decode($product['Specification_1'], true);


        $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :ID";
        $priceStmt = $pdo->prepare($priceSql);
        $priceStmt->bindValue(':ID', $productId);
        $priceStmt->execute();
        $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

        $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :ID";
        $imageStmt = $pdo->prepare($imageSql);
        $imageStmt->bindValue(':ID', $keyword);
        $imageStmt->execute();
        $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($imageDatas as $imageData) {
            if ($imageData) {
                // Replace backslashes with forward slashes
                // $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                // $product['Image_Path'] = $imagePath;
                $imageData['product_image_Path'] = str_replace('\\', '/', $imageData['product_image_Path']);
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                // $product['Image_Path'] = null;
                $imageData['product_image_Path'] = null;
            } 
        }     

        // Fetch and paginate reviews for this product
        $reviewsList = new \Pimcore\Model\DataObject\DbProductReview\Listing();
        $reviewsList->setCondition("UniqueId = ?", [$keyword]);
        
        $pagination = $paginator->paginate(
            $reviewsList,
            $request->query->getInt('page', 1),
            5 // 5 reviews per page
        );

        // Calculate average rating
        $averageRating = 0;
        $totalReviews = $reviewsList->getCount();
        if ($totalReviews > 0) {
            $totalRating = 0;
            $allReviews = $reviewsList->load();
            foreach ($allReviews as $review) {
                $totalRating += $review->getRating();
            }
            $averageRating = round($totalRating / $totalReviews, 1);
        }
    
        return $this->render('Professional/ProductsSinglePage.html.twig', [
            'product' => $product,
            'PriceData' => $priceData,
            'ImageDatas' => $imageDatas,
            'specifications' => $specifications,
            'reviews' => $pagination,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }
        



    /**
     * @Route("/categories/{url}", name="Products_Categories")
     * @Route("/categories/{url}/load-more", name="Products_Categories_Load_More")
     */
    public function productsCategoriesSinglePageAction($url, Security $security, SessionInterface $session, Request $request, LoggerInterface $mailer)
    {   
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $slug = $url;

            // Define default values
            $defaultMin = 0;
            $defaultMax = 10000000;
            $defaultSort = 'default';

            // Get filter parameters from session or set default values
            $minPrice = $session->get('minPrice', $defaultMin);
            $maxPrice = $session->get('maxPrice', $defaultMax);
            $sortOption = $session->get('sortOption', $defaultSort);

            $form = $this->createForm(ProductsPageFilterFormType::class);
            $form->handleRequest($request);

            // Get POST parameters or set default values
            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();

                $minPrice = $formData['min'] ?? $defaultMin;
                $maxPrice = $formData['max'] ?? $defaultMax;
                $sortOption = $formData['sort'] ?? $defaultSort;

                // Store filter parameters in session
                $session->set('minPrice', $minPrice);
                $session->set('maxPrice', $maxPrice);
                $session->set('sortOption', $sortOption);
            }

            // First get the category name from the slug
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $categorySql = "SELECT Category_Name FROM Product_Categories WHERE LOWER(Category_Slug) = LOWER(:category_slug)";
            $categoryStmt = $pdo->prepare($categorySql);
            $categoryStmt->bindValue(':category_slug', $slug);
            $categoryStmt->execute();
            $categoryData = $categoryStmt->fetch(\PDO::FETCH_ASSOC);
            $category = $categoryData ? $categoryData['Category_Name'] : null;

            if (!$category) {
                return $this->render('Architect/NotLogged_signup.html.twig');
            }

            // First, search ProProducts that match the category
            $proProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
            $proProductsList->setCondition("LOWER(ParentCategory) = LOWER(:category) OR 
                                        LOWER(SubCategory) = LOWER(:category) OR 
                                        LOWER(SubSubCategory) = LOWER(:category)", 
                                    ['category' => $category]);
            $proProducts = $proProductsList->load();

            // Convert ProProducts to array format
            $proProductsArray = [];
            foreach ($proProducts as $product) {
                $proProfile = $product->getProfessional();
                $portfolioType = $proProfile ? strtolower($proProfile->getPortfolioType()) : '';
                $imagePath = $this->getProductImagePath($product, null);
                
                $proProductsArray[] = [
                    'Unique_ID' => $product->getKey(),
                    'Product_Name' => $product->getProductName(),
                    'Product_Price' => $product->getPrice(),
                    'Product_Unit' => $product->getUnit() ?: 'unit',
                    'Image_Path' => $imagePath,
                    'isProProduct' => true,
                    'viewLink' => '/' . $portfolioType . '/product/' . $product->getKey()
                ];
            }

            // Filter ProProducts by price range
            $filteredProProducts = array_filter($proProductsArray, function($product) use ($minPrice, $maxPrice) {
                return $product['Product_Price'] >= $minPrice && $product['Product_Price'] <= $maxPrice;
            });

            // For AJAX requests (load more)
            if ($request->isXmlHttpRequest() || $request->get('_route') === 'Products_Categories_Load_More') {
                $offset = $request->query->get('offset', 0);
                $limit = 10;
                
                // Base query for scraped products
                $sql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                        pc.Product_Price, 
                        pc.Product_Unit,
                        pi.product_image_Path as Image_Path
                    FROM products p
                    JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE p.Product_Category = :category
                    AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
                
                // Add sorting
                switch ($sortOption) {
                    case 'priceLowHigh':
                        $sql .= " ORDER BY pc.Product_Price ASC";
                        break;
                    case 'priceHighLow':
                        $sql .= " ORDER BY pc.Product_Price DESC";
                        break;
                    default:
                        $sql .= " ORDER BY p.ID ASC";
                        break;
                }
                
                $sql .= " LIMIT :limit OFFSET :offset";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':category', $category, \PDO::PARAM_STR);
                $stmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
                $stmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $stmt->execute();
                
                $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach ($products as &$product) {
                    if ($product['Image_Path']) {
                        $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
                    }
                    $product['isProProduct'] = false;
                    $product['viewLink'] = '/products/' . $product['Unique_ID'];
                }
                
                // For AJAX, only return scraped products (ProProducts are already loaded)
                $html = $this->renderView('Professional/product_items.html.twig', [
                    'products' => $products
                ]);
                
                $countSql = "SELECT COUNT(p.ID) as total 
                            FROM products p
                            JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                            WHERE p.Product_Category = :category
                            AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
                $countStmt = $pdo->prepare($countSql);
                $countStmt->bindValue(':category', $category, \PDO::PARAM_STR);
                $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
                $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
                $countStmt->execute();
                $total = $countStmt->fetchColumn();
                
                return new JsonResponse([
                    'html' => $html,
                    'hasMore' => ($offset + $limit) < $total
                ]);
            }

            // For initial page load - get first 10 scraped products
            $initialSql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                            pc.Product_Price, 
                            pc.Product_Unit,
                            pi.product_image_Path as Image_Path
                    FROM products p
                    JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE p.Product_Category = :category
                    AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
            
            switch ($sortOption) {
                case 'priceLowHigh':
                    $initialSql .= " ORDER BY pc.Product_Price ASC";
                    break;
                case 'priceHighLow':
                    $initialSql .= " ORDER BY pc.Product_Price DESC";
                    break;
                default:
                    $initialSql .= " ORDER BY p.ID ASC";
                    break;
            }
            
            $initialSql .= " LIMIT 10";
            
            $initialStmt = $pdo->prepare($initialSql);
            $initialStmt->bindValue(':category', $category, \PDO::PARAM_STR);
            $initialStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $initialStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $initialStmt->execute();
            
            $initialProducts = $initialStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($initialProducts as &$product) {
                if ($product['Image_Path']) {
                    $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
                }
                $product['isProProduct'] = false;
                $product['viewLink'] = '/products/' . $product['Unique_ID'];
            }

            // Merge ProProducts and scraped products
            $allProducts = array_merge($filteredProProducts, $initialProducts);

            // Apply sorting to combined results
            switch ($sortOption) {
                case 'priceLowHigh':
                    usort($allProducts, fn($a, $b) => $a['Product_Price'] <=> $b['Product_Price']);
                    break;
                case 'priceHighLow':
                    usort($allProducts, fn($a, $b) => $b['Product_Price'] <=> $a['Product_Price']);
                    break;
            }

            // Get total count for initial load
            $countSql = "SELECT COUNT(p.ID) as total 
                        FROM products p
                        JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                        WHERE p.Product_Category = :category
                        AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->bindValue(':category', $category, \PDO::PARAM_STR);
            $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $countStmt->execute();
            $totalProducts = $countStmt->fetchColumn();

            return $this->render('Professional/Category_products_list.html.twig', [
                'form' => $form->createView(),
                'products' => $allProducts,
                'totalProducts' => $totalProducts + count($filteredProProducts),
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'sortOption' => $sortOption,
                'Category' => $category,
                'categorySlug' => $slug,
            ]);
        }
        return $this->render('Architect/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/{customertype}/projects-bulk-upload", name="projects-bulk-upload", requirements={"customertype"="contractor|designer|architect|builder|engineer"})
     */
    public function projectsBulkUpload(
        string $customertype,
        Security $security, 
        Request $request, 
        LoggerInterface $logger, 
        SessionInterface $session
    ) {
        $user = $security->getUser();
        $customer = $user;
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $form = $this->createForm(BulkProjectsFormType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                // Get uploaded file
                $formData = $form->getData();
                $file = $formData['ProjectsexcelFile'];
                $ProjectUser = $formData['ProfessionalID'];
                $imageAssetPath = $formData['ImageAssetPath']; // New field for image asset path
                
                $ProProfile = ProProfile::getByPath("/Services/" . ucfirst($customertype) . "s/Profiles/$ProjectUser");
                $logger->info('ProProfile Path: ' . $ProProfile);
                $logger->info('Image Asset Path: ' . $imageAssetPath);
                
                // Define upload path
                $filePath = $file->getPathname();
                
                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                
                    // Validate if the file contains the expected format
                    if (count($rows) < 2) {
                        $this->addFlash('error', 'The uploaded Excel file contains no data.');
                        return $this->redirectToRoute('projects-bulk-upload', ['customertype' => $customertype]);
                    }
                
                    // Process rows, skipping the header row
                    foreach ($rows as $index => $row) {
                        if ($index === 0) continue; // Skip header row
                        
                        // Skip empty rows
                        if (empty($row[0]) && empty($row[1])) continue;
                
                        $proProject = new ProProject();
                        $projectName = $row[1] ?? ''; // Project Title column
                
                        // Skip if no project name
                        if (empty($projectName)) continue;
                
                        // Generate key
                        $timestamp = time();
                        $logger->info('TimeStamp: ' . $timestamp);

                        $randomDigits = rand(100, 999);
                        $key = strtolower(str_replace(' ', '-', $projectName)) . '-' . $timestamp . $randomDigits;
                        $proProject->setKey($key);
                
                        // Set path - save to customer type specific folder
                        $proProject->setParent(DataObject::getByPath('/Services/' . ucfirst($customertype) . 's/Projects/'));
                
                        // Populate object fields based on Excel columns
                        $proProject->setProjectName($row[1] ?? null);        // Column 2: Project Title
                        $proProject->setLocation($row[2] ?? null);           // Column 3: Project Location
                        $proProject->setProjectDescription($row[3] ?? null); // Column 4: Project Description
                        $proProject->setMinPrice((float)($row[4] ?? 0));     // Column 5: Project Value
                        $proProject->setConfiguration($row[5] ?? null);      // Column 6: Project Specification
                        $proProject->setCollaborations($row[6] ?? null);     // Column 7: Collaborations & Credits
                        
                        $proProject->setProfessional($ProProfile);
                        $proProject->setProfessionalPath($ProProfile);
                        
                        // Process image gallery from column 1 (Project Gallery)
                        $imageName = $row[0] ?? null; // Image name from Excel
                        $logger->info('Image name From Excel: ' . $imageName);
                        
                        // Process image based on image name and asset path
                        if (!empty($imageName) && !empty($imageAssetPath)) {
                            $logger->info('Processing image for project: ' . $projectName);
                            $logger->info('Image name from Excel: ' . $imageName);
                            $logger->info('Image asset path: ' . $imageAssetPath);
                            
                            // Ensure asset path starts and ends with forward slash
                            $normalizedPath = rtrim(ltrim($imageAssetPath, '/'), '/');
                            $fullAssetPath = '/' . $normalizedPath . '/' . $imageName;
                            
                            $logger->info('Full asset path: ' . $fullAssetPath);
                            
                            try {
                                // Try to get the existing asset
                                $existingAsset = \Pimcore\Model\Asset::getByPath($fullAssetPath);
                                
                                if ($existingAsset && $existingAsset instanceof \Pimcore\Model\Asset\Image) {
                                    $logger->info('Found existing asset: ' . $fullAssetPath);
                                    
                                    // Create ImageGallery with single image for ProjectGallery field
                                    $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                                    $hotspotImage->setImage($existingAsset);
                                    
                                    $items = [$hotspotImage];
                                    $imageGallery = new \Pimcore\Model\DataObject\Data\ImageGallery($items);
                                    $proProject->setProjectGallery($imageGallery);
                                    
                                } else {
                                    $logger->warning('Asset not found at path: ' . $fullAssetPath . ' for project: ' . $projectName);
                                    // Optionally, you can add a flash message for missing images
                                    // $this->addFlash('warning', 'Image not found for project: ' . $projectName . ' at path: ' . $fullAssetPath);
                                }
                            } catch (\Exception $e) {
                                $logger->error('Error processing image for project ' . $projectName . ': ' . $e->getMessage());
                            }
                        } else {
                            if (empty($imageName)) {
                                $logger->info('No image name provided for project: ' . $projectName);
                            }
                            if (empty($imageAssetPath)) {
                                $logger->info('No image asset path provided for project: ' . $projectName);
                            }
                        }
                
                        // Save object
                        $proProject->setPublished(true);
                        $proProject->save();
                        $logger->info('Project saved successfully: ' . $proProject->getKey());
                    }
                
                    $this->addFlash('success', 'Projects have been successfully uploaded and created.');
                } catch (\Exception $e) {
                    $logger->error('Error uploading projects: ' . $e->getMessage());
                    $this->addFlash('error', 'An error occurred while processing the file. Please check the file format and data.');
                }
                
                return $this->redirectToRoute('projects-bulk-upload', ['customertype' => $customertype]);
            }
            
            return $this->render('/Professional/Dashboard/Projects_upload_bulk.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
                'customertype' => $customertype,
            ]);
        }
        
        return $this->render('Professional/NotLogged_signup.html.twig');
    }





    /**
     * @Route("/categories/{url}/{subcategories}", name="Products_Sub_Categories")
     * @Route("/categories/{url}/{subcategories}/load-more", name="Products_Sub_Categories_Load_More")
     */
    public function productsSubCategoriesSinglePageAction($url, $subcategories, Security $security, SessionInterface $session, Request $request, LoggerInterface $mailer)
    {   
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $slug = $url;
            $catSlug = str_replace('-', ' ', $slug);
            $subslug = $subcategories;
            $subCatslug = str_replace('-', ' ', $subslug);
            $subCatslug = str_replace('   ', ' - ', $subCatslug);
            $subCatslug = str_replace('%28', '(', $subCatslug);
            $subCatslug = str_replace('%29', ')', $subCatslug);
            $subCatslug = str_replace('%2F', '/', $subCatslug);

            // Define default values
            $defaultMin = 0;
            $defaultMax = 10000000;
            $defaultSort = 'default';

            // Get filter parameters from session or set default values
            $minPrice = $session->get('minPrice', $defaultMin);
            $maxPrice = $session->get('maxPrice', $defaultMax);
            $sortOption = $session->get('sortOption', $defaultSort);

            $form = $this->createForm(ProductsPageFilterFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $minPrice = $formData['min'] ?? $defaultMin;
                $maxPrice = $formData['max'] ?? $defaultMax;
                $sortOption = $formData['sort'] ?? $defaultSort;
                $session->set('minPrice', $minPrice);
                $session->set('maxPrice', $maxPrice);
                $session->set('sortOption', $sortOption);
            }

            // First, search ProProducts that match the subcategory
            $proProductsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
            $proProductsList->setCondition("LOWER(SubCategory) = LOWER(:subcategory) OR 
                                        LOWER(SubSubCategory) = LOWER(:subcategory)", 
                                    ['subcategory' => $subCatslug]);
            $proProducts = $proProductsList->load();

            // Convert ProProducts to array format
            $proProductsArray = [];
            foreach ($proProducts as $product) {
                $proProfile = $product->getProfessional();
                $portfolioType = $proProfile ? strtolower($proProfile->getPortfolioType()) : '';
                $imagePath = $this->getProductImagePath($product, null);
                
                $proProductsArray[] = [
                    'Unique_ID' => $product->getKey(),
                    'Product_Name' => $product->getProductName(),
                    'Product_Price' => $product->getPrice(),
                    'Product_Unit' => $product->getUnit() ?: 'unit',
                    'Image_Path' => $imagePath,
                    'isProProduct' => true,
                    'viewLink' => '/' . $portfolioType . '/product/' . $product->getKey()
                ];
            }

            // Filter ProProducts by price range
            $filteredProProducts = array_filter($proProductsArray, function($product) use ($minPrice, $maxPrice) {
                return $product['Product_Price'] >= $minPrice && $product['Product_Price'] <= $maxPrice;
            });

            // Database connection for scraped products
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // For AJAX requests (load more)
            if ($request->isXmlHttpRequest() || $request->get('_route') === 'Products_Sub_Categories_Load_More') {
                $offset = $request->query->get('offset', 0);
                $limit = 10;
                
                $sql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                        pc.Product_Price, 
                        pc.Product_Unit,
                        pi.product_image_Path as Image_Path
                    FROM products p
                    JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE p.Product_SubCategory = :subcategory
                    AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
                
                switch ($sortOption) {
                    case 'priceLowHigh':
                        $sql .= " ORDER BY pc.Product_Price ASC";
                        break;
                    case 'priceHighLow':
                        $sql .= " ORDER BY pc.Product_Price DESC";
                        break;
                    default:
                        $sql .= " ORDER BY p.ID ASC";
                        break;
                }
                
                $sql .= " LIMIT :limit OFFSET :offset";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':subcategory', $subCatslug, \PDO::PARAM_STR);
                $stmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
                $stmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $stmt->execute();
                
                $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach ($products as &$product) {
                    if ($product['Image_Path']) {
                        $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
                    }
                    $product['isProProduct'] = false;
                    $product['viewLink'] = '/products/' . $product['Unique_ID'];
                }
                
                $html = $this->renderView('Professional/product_items.html.twig', [
                    'products' => $products
                ]);
                
                $countSql = "SELECT COUNT(p.ID) as total 
                            FROM products p
                            JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                            WHERE p.Product_SubCategory = :subcategory
                            AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
                $countStmt = $pdo->prepare($countSql);
                $countStmt->bindValue(':subcategory', $subCatslug, \PDO::PARAM_STR);
                $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
                $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
                $countStmt->execute();
                $total = $countStmt->fetchColumn();
                
                return new JsonResponse([
                    'html' => $html,
                    'hasMore' => ($offset + $limit) < $total
                ]);
            }

            // For initial page load - get first 10 scraped products
            $initialSql = "SELECT p.ID, p.Unique_ID, p.Product_Name, 
                            pc.Product_Price, 
                            pc.Product_Unit,
                            pi.product_image_Path as Image_Path
                    FROM products p
                    JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                    LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID
                    WHERE p.Product_SubCategory = :subcategory
                    AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
            
            switch ($sortOption) {
                case 'priceLowHigh':
                    $initialSql .= " ORDER BY pc.Product_Price ASC";
                    break;
                case 'priceHighLow':
                    $initialSql .= " ORDER BY pc.Product_Price DESC";
                    break;
                default:
                    $initialSql .= " ORDER BY p.ID ASC";
                    break;
            }
            
            $initialSql .= " LIMIT 10";
            
            $initialStmt = $pdo->prepare($initialSql);
            $initialStmt->bindValue(':subcategory', $subCatslug, \PDO::PARAM_STR);
            $initialStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $initialStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $initialStmt->execute();
            
            $initialProducts = $initialStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($initialProducts as &$product) {
                if ($product['Image_Path']) {
                    $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
                }
                $product['isProProduct'] = false;
                $product['viewLink'] = '/products/' . $product['Unique_ID'];
            }

            // Merge ProProducts and scraped products
            $allProducts = array_merge($filteredProProducts, $initialProducts);

            // Apply sorting to combined results
            switch ($sortOption) {
                case 'priceLowHigh':
                    usort($allProducts, fn($a, $b) => $a['Product_Price'] <=> $b['Product_Price']);
                    break;
                case 'priceHighLow':
                    usort($allProducts, fn($a, $b) => $b['Product_Price'] <=> $a['Product_Price']);
                    break;
            }

            // Get total count for initial load
            $countSql = "SELECT COUNT(p.ID) as total 
                        FROM products p
                        JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                        WHERE p.Product_SubCategory = :subcategory
                        AND pc.Product_Price BETWEEN :minPrice AND :maxPrice";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->bindValue(':subcategory', $subCatslug, \PDO::PARAM_STR);
            $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $countStmt->execute();
            $totalProducts = $countStmt->fetchColumn();

            return $this->render('Professional/sub_Category_products_list.html.twig', [
                'form' => $form->createView(),
                'products' => $allProducts,
                'totalProducts' => $totalProducts + count($filteredProProducts),
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'sortOption' => $sortOption,
                'Category' => ucwords($subCatslug),
                'categorySlug' => $slug,
                'subcategorySlug' => $subslug,
            ]);
        }
        return $this->render('Architect/NotLogged_signup.html.twig');
    }
    

    // /**
    //  * @Route("/products-search/{url}", name="Products_Search")
    //  */
    // public function productsSearchPageAction($url, Security $security, SessionInterface $session, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    // {   
    //     $slug = $url;

    //     // Define default values
    //     $defaultMin = 0;
    //     $defaultMax = 10000000;
    //     $defaultSort = 'default';

    //     // Get filter parameters from session or set default values
    //     // $session = $this->session ;

    //     // $session = $this->get('session');
    //     $minPrice = $session->get('minPrice', $defaultMin);
    //     $maxPrice = $session->get('maxPrice', $defaultMax);
    //     $sortOption = $session->get('sortOption', $defaultSort);

    //     $form = $this->createForm(ProductsPageFilterFormType::class);
    //     $form->handleRequest($request);

    //     // Get POST parameters or set default values
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $formData = $form->getData();

    //         $minPrice = $formData['min'] ?? $defaultMin;
    //         $maxPrice = $formData['max'] ?? $defaultMax;
    //         $sortOption = $formData['sort'] ?? $defaultSort;

    //         // Store filter parameters in session
    //         $session->set('minPrice', $minPrice);
    //         $session->set('maxPrice', $maxPrice);
    //         $session->set('sortOption', $sortOption);
    //     }

    //     // Database connection
    //     $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
    //     $username = 'pimcoreuser';
    //     $password = 'G0H0me@T0day';
    //     $pdo = new \PDO($dsn, $username, $password);
    //     $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    //     // Fetch product IDs and names
    //     $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
    //     $stmt = $pdo->prepare($sql);
    //     $stmt->bindValue(':category_slug', $slug);
    //     $stmt->execute();
    //     $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

    //     if ($categoryData) {  // Checks if $categoryData is not false
    //         $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
    //     } else {
    //         // Handle the case where no category was found
    //         $category = null;
    //         // Optionally, you can set a default category or handle this case differently
    //     }

    //     // Fetch product IDs and names
    //     // $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
    //     // $prodstmt = $pdo->prepare($ProdSql);
    //     // $prodstmt->bindValue(':Product_Category', $category);
    //     // $prodstmt->execute();
    //     // $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);

    //     // Fetch product IDs and names where the title contains the search term

    //     $ProdSql = "SELECT ID, Unique_ID, Product_Name 
    //         FROM products 
    //         WHERE Product_Name COLLATE utf8mb4_general_ci LIKE :searchTerm";
    //     $prodstmt = $pdo->prepare($ProdSql);
    //     $prodstmt->bindValue(':searchTerm', '%' . $url . '%', PDO::PARAM_STR);
    //     $prodstmt->execute();
    //     $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);


    //     // Fetch product prices
    //     foreach ($products as &$product) {
    //         // Logging the entire product array to understand its structure
    //         // $logger->info('Product Array:', ['product' => $product]);

    //         $productId = $product['ID'];
    //         $uniqueId = $product['Unique_ID'];
    //         // $logger->info("Product ID: $productId");

    //         $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
    //         $priceStmt = $pdo->prepare($priceSql);
    //         $priceStmt->bindValue(':productId', $productId);
    //         $priceStmt->execute();
    //         $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

    //         $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
    //         $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
    //         // $logger->info("Product Price: " . $product['Product_Price']);


    //         $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
    //         $imageStmt = $pdo->prepare($imageSql);
    //         $imageStmt->bindValue(':productId', $uniqueId);
    //         $imageStmt->execute();
    //         $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
    //         if (count($imageDatas) > 0) {
    //             $imageData = $imageDatas[0];
    //             // Now you can safely use $imageData['product_image_Path']
    //         } else {
    //             // Handle the case where no image was found
    //             $imageData = null;
    //             // Optionally, you can set a default image or handle this case differently
    //         }

    //         if ($imageData) {
    //             // Replace backslashes with forward slashes
    //             $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
    //             $product['Image_Path'] = $imagePath;
    //             // $logger->info("Product Image Path: " . $product['Image_Path']);
    //         } else {
    //             $product['Image_Path'] = null;
    //         }           
            
    //     }        
    
    //     // Filter by price range
        
    //     $products = array_filter($products, function ($product) use ($minPrice, $maxPrice) {
    //         return $product['Product_Price'] >= $minPrice && $product['Product_Price'] <= $maxPrice;
    //     });
        
    
    //     // Sort based on selected option
    //     switch ($sortOption) {
    //         case 'priceLowHigh':
    //             usort($products, function ($a, $b) {
    //                 return $a['Product_Price'] <=> $b['Product_Price'];
    //             });
    //             break;
    //         case 'priceHighLow':
    //             usort($products, function ($a, $b) {
    //                 return $b['Product_Price'] <=> $a['Product_Price'];
    //             });
    //             break;
            
    //         default:
    //             // No sorting
    //             break;
    //     }

    //     // Pagination
    //     $pagination = $paginator->paginate(
    //         $products,
    //         $request->query->getInt('page', 1),
    //         10  // Number of items per page
    //     );
    //     $paginationVariables = $pagination->getPaginationData();

    //     $paginationVariables['url'] = $this->generateUrl('Products-List', [
    //         'minPrice' => $minPrice,
    //         'maxPrice' => $maxPrice,
    //         'sortOption' => $sortOption,
    //     ]);

    //     // Render the template
    //     return $this->render('Professional/Category_products_list.html.twig', [
    //         'form' => $form->createView(),
    //         'products' => $pagination,
    //         'paginationVariables' => $paginationVariables,
    //         'minPrice' => $minPrice,
    //         'maxPrice' => $maxPrice,
    //         'sortOption' => $sortOption,
    //         'Category' => $category,
    //     ]);
    // }

    
    /**
     * @Route("/products-search", name="Products_Search", methods={"GET", "POST"})
     */
    public function productsSearchPageAction(Request $request, Security $security, SessionInterface $session, PaginatorInterface $paginator, LoggerInterface $logger)
    {
        // Handle search query
        $searchQuery = trim($request->request->get('search', ''));
        $logger->info('Search Query Captured:', ['search' => $searchQuery]);
        
        // Split into search terms and convert to lowercase
        $searchTerms = preg_split('/\s+/', strtolower($searchQuery));
        
        // Default values
        $defaultMin = 0;
        $defaultMax = 10000000;
        $defaultSort = 'default';
        $minPrice = $session->get('minPrice', $defaultMin);
        $maxPrice = $session->get('maxPrice', $defaultMax);
        $sortOption = $session->get('sortOption', $defaultSort);

        $form = $this->createForm(ProductsPageFilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $minPrice = $formData['min'] ?? $defaultMin;
            $maxPrice = $formData['max'] ?? $defaultMax;
            $sortOption = $formData['sort'] ?? $defaultSort;
            $session->set('minPrice', $minPrice);
            $session->set('maxPrice', $maxPrice);
            $session->set('sortOption', $sortOption);
        }

        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Base query to fetch all products with their details including description
        $sql = "SELECT p.ID, p.Unique_ID, p.Product_Name, p.Product_Description,
                    pc.Product_Price, pc.Product_Unit,
                    pi.product_image_Path as Image_Path,
                    p.Product_Category, p.Product_SubCategory
                FROM products p
                JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                LEFT JOIN product_images pi ON p.Unique_ID = pi.Unique_ID";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $allProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Score products based on search relevance
        $scoredProducts = array_map(function ($product) use ($searchTerms, $searchQuery) {
            $score = 0;
            $productName = strtolower($product['Product_Name']);
            $description = strtolower($product['Product_Description'] ?? '');
            $category = strtolower($product['Product_Category']);
            $subCategory = strtolower($product['Product_SubCategory']);
            
            // Exact match boost (highest priority)
            if (strtolower($searchQuery) === $productName) {
                $score += 100;
            }
            
            // Exact match in description (high priority)
            if (strtolower($searchQuery) === $description) {
                $score += 80;
            }
            
            // All terms match in product name (high priority)
            if ($this->allTermsMatch($searchTerms, $productName)) {
                $score += 50;
            }
            
            // All terms match in description (medium-high priority)
            if ($this->allTermsMatch($searchTerms, $description)) {
                $score += 40;
            }
            
            // All terms match anywhere (medium priority)
            $fullText = "$productName $description $category $subCategory";
            if ($this->allTermsMatch($searchTerms, $fullText)) {
                $score += 30;
            }
            
            // Count matching terms with different weights
            $matchedTerms = 0;
            foreach ($searchTerms as $term) {
                // Product name matches get highest weight
                if (strpos($productName, $term) !== false) {
                    $matchedTerms++;
                    $score += 10;
                } 
                // Description matches get medium weight
                elseif (strpos($description, $term) !== false) {
                    $matchedTerms++;
                    $score += 7;
                }
                // Category matches get lower weight
                elseif (strpos($category, $term) !== false) {
                    $matchedTerms++;
                    $score += 5;
                }
                // Subcategory matches get lowest weight
                elseif (strpos($subCategory, $term) !== false) {
                    $matchedTerms++;
                    $score += 3;
                }
            }
            
            // Partial score based on how many terms matched
            if ($matchedTerms > 0) {
                $score += ($matchedTerms / count($searchTerms)) * 20;
            }
            
            // Bonus for matches at the beginning of product name
            foreach ($searchTerms as $term) {
                if (strpos($productName, $term) === 0) {
                    $score += 5;
                }
            }
            
            return [
                'product' => $product,
                'score' => $score,
                'matchedTerms' => $matchedTerms
            ];
        }, $allProducts);

        // Filter out zero-score results and sort by score
        $filteredProducts = array_filter($scoredProducts, function ($item) {
            return $item['score'] > 0;
        });
        
        usort($filteredProducts, function ($a, $b) {
            // First sort by score descending
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }
            // For equal scores, sort by number of matched terms
            return $b['matchedTerms'] <=> $a['matchedTerms'];
        });

        // Extract just the products for further processing
        $sortedProducts = array_column($filteredProducts, 'product');

        // Process image paths and filter by price range
        $processedProducts = [];
        foreach ($sortedProducts as &$product) {
            if ($product['Image_Path']) {
                $product['Image_Path'] = str_replace('\\', '/', $product['Image_Path']);
            }
            
            // Filter by price range
            if ($product['Product_Price'] >= $minPrice && $product['Product_Price'] <= $maxPrice) {
                $processedProducts[] = $product;
            }
        }

        // Apply sorting
        switch ($sortOption) {
            case 'priceLowHigh':
                usort($processedProducts, fn($a, $b) => $a['Product_Price'] <=> $b['Product_Price']);
                break;
            case 'priceHighLow':
                usort($processedProducts, fn($a, $b) => $b['Product_Price'] <=> $a['Product_Price']);
                break;
        }

        // Pagination
        $pagination = $paginator->paginate(
            $processedProducts,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        $paginationVariables['url'] = $this->generateUrl('Products_Search', [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
        ]);

        return $this->render('Professional/Search_products_list.html.twig', [
            'form' => $form->createView(),
            'products' => $pagination,
            'paginationVariables' => $paginationVariables,
            'searchQuery' => $searchQuery,
            'Category' => "Search Results for: " . $searchQuery,
        ]);
    }



    /**
     * @Route("/luxury/products", name="luxury-Products-List")
     */
    public function LuxuryProductsListAction( Request $request, SessionInterface $session, PaginatorInterface $paginator, LoggerInterface $logger)
    {   

        // Define default values
        $defaultMin = 0;
        $defaultMax = 10000000;
        $defaultSort = 'default';

        // Get filter parameters from session or set default values
        // $session = $this->session ;

        $metaTitle = "Construction Materials Online | Cement, Tiles, Steel Bars & More | ArQonZ"; 
        $metaDescription = "Explore a wide range of high-quality Luxury construction materials on ArQonZ. From cement and TMT steel bars to tiles, plumbing, and electricals, find everything you need to build your dream project.";

        // $session = $this->get('session');
        $minPrice = $session->get('minPrice', $defaultMin);
        $maxPrice = $session->get('maxPrice', $defaultMax);
        $sortOption = $session->get('sortOption', $defaultSort);

        
        $form = $this->createForm(ProductsPageFilterFormType::class);
        $form->handleRequest($request);

        // Get POST parameters or set default values
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $minPrice = $formData['min'] ?? $defaultMin;
            $maxPrice = $formData['max'] ?? $defaultMax;
            $sortOption = $formData['sort'] ?? $defaultSort;

            // Store filter parameters in session
            $session->set('minPrice', $minPrice);
            $session->set('maxPrice', $maxPrice);
            $session->set('sortOption', $sortOption);
        }
        
        $productsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $productsList->addConditionParam(
            "ProfessionalPath IN (?, ?)", 
            ["/Services/Distributors/Profiles/MMC-Group-1733912539", "/Services/Dealers/Profiles/Bathcaff-1735563961"]
        );
        $products = $productsList->load();


        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        

        $Categoriessql = "SELECT Category_Id, Category_Name, Category_Slug, Category_Image_Path FROM Product_Categories";
        $Categoriesstmt = $pdo->prepare($Categoriessql);
        $Categoriesstmt->execute();
        $Categories = $Categoriesstmt->fetchAll(\PDO::FETCH_ASSOC);

        

        // Log the entire products array
        // $logger->info('Products Array:', ['products' => $products]);

       
    
        // Filter by price range
        
        $products = array_filter($products, function ($product) use ($minPrice, $maxPrice) {
            return $product->getPrice() >= $minPrice && $product->getPrice() <= $maxPrice;
        });
        
    
        // Sort based on selected option
        switch ($sortOption) {
            case 'priceLowHigh':
                usort($products, function ($a, $b) {
                    return $a->getPrice() <=> $b->getPrice();
                });
                break;
            case 'priceHighLow':
                usort($products, function ($a, $b) {
                    return $b->getPrice() <=> $a->getPrice();
                });
                break;
            
            default:
                // No sorting
                break;
        }

        // Pagination
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        $paginationVariables['url'] = $this->generateUrl('Products-List', [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
        ]);

        // Render the template
        return $this->render('Professional/luxury_products_list.html.twig', [
            'form' => $form->createView(),
            'products' => $pagination,
            'paginationVariables' => $paginationVariables,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
            'Categories' => $Categories,
            'metadescription' => $metaDescription,
            'metatitle' => $metaTitle,
        ]);
    }


    /**
     * @Route("/luxury-products/{url}", name="Luxury_Products_Single_Page")
     */
    public function luxuryproductsSinglePageAction($url, Security $security, LoggerInterface $logger, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $keyword = $url;

        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $productsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        // $productsList->addConditionParam("ObjKey = ?", $url);

        $products = $productsList->load();

        
        foreach ($products as $prod) {
            if ($prod->getObjKey() === $keyword) {
                $product = $prod; // Save the matched product object
                break; // Exit the loop once found
            }
        }

        if ($product) {
            $productname = $product->getProductName();
        } else {
            $productname = "No product found with the given ObjKey.";
        }

        $logger->info('product Object', [
            'product' => $productname,
            
        ]);
    
        return $this->render('Professional/LuxuryProductsSinglePage.html.twig', [
            'product' => $product,
            
        ]);
    }
    

    /**
     * @Route("/international/products", name="international-Products-List")
     */
    public function InternationalProductsListAction( Request $request, SessionInterface $session, PaginatorInterface $paginator, LoggerInterface $logger)
    {   

        // Define default values
        $defaultMin = 0;
        $defaultMax = 10000000;
        $defaultSort = 'default';

        // Get filter parameters from session or set default values
        // $session = $this->session ;

        $metaTitle = "Construction Materials Online | Cement, Tiles, Steel Bars & More | ArQonZ"; 
        $metaDescription = "Explore a wide range of high-quality International construction materials on ArQonZ. From cement and TMT steel bars to tiles, plumbing, and electricals, find everything you need to build your dream project.";

        // $session = $this->get('session');
        $minPrice = $session->get('minPrice', $defaultMin);
        $maxPrice = $session->get('maxPrice', $defaultMax);
        $sortOption = $session->get('sortOption', $defaultSort);

        
        $form = $this->createForm(ProductsPageFilterFormType::class);
        $form->handleRequest($request);

        // Get POST parameters or set default values
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $minPrice = $formData['min'] ?? $defaultMin;
            $maxPrice = $formData['max'] ?? $defaultMax;
            $sortOption = $formData['sort'] ?? $defaultSort;

            // Store filter parameters in session
            $session->set('minPrice', $minPrice);
            $session->set('maxPrice', $maxPrice);
            $session->set('sortOption', $sortOption);
        }
        
        $productsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        $productsList->addConditionParam("InternationalBrand = ?", "1");
        $products = $productsList->load();


        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        

        $Categoriessql = "SELECT Category_Id, Category_Name, Category_Slug, Category_Image_Path FROM Product_Categories";
        $Categoriesstmt = $pdo->prepare($Categoriessql);
        $Categoriesstmt->execute();
        $Categories = $Categoriesstmt->fetchAll(\PDO::FETCH_ASSOC);

        

        // Log the entire products array
        // $logger->info('Products Array:', ['products' => $products]);

       
    
        // Filter by price range
        
        $products = array_filter($products, function ($product) use ($minPrice, $maxPrice) {
            return $product->getPrice() >= $minPrice && $product->getPrice() <= $maxPrice;
        });
        
    
        // Sort based on selected option
        switch ($sortOption) {
            case 'priceLowHigh':
                usort($products, function ($a, $b) {
                    return $a->getPrice() <=> $b->getPrice();
                });
                break;
            case 'priceHighLow':
                usort($products, function ($a, $b) {
                    return $b->getPrice() <=> $a->getPrice();
                });
                break;
            
            default:
                // No sorting
                break;
        }

        // Pagination
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        $paginationVariables['url'] = $this->generateUrl('Products-List', [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
        ]);

        // Render the template
        return $this->render('Professional/international_products_list.html.twig', [
            'form' => $form->createView(),
            'products' => $pagination,
            'paginationVariables' => $paginationVariables,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortOption' => $sortOption,
            'Categories' => $Categories,
            'metadescription' => $metaDescription,
            'metatitle' => $metaTitle,
        ]);
    }



    /**
     * @Route("/international-products/{url}", name="international_Products_Single_Page")
     */
    public function linternationalproductsSinglePageAction($url, Security $security, LoggerInterface $logger, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $keyword = $url;

        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $productsList = new \Pimcore\Model\DataObject\ProProduct\Listing();
        // $productsList->addConditionParam("ObjKey = ?", $url);

        $products = $productsList->load();

        
        foreach ($products as $prod) {
            if ($prod->getObjKey() === $keyword) {
                $product = $prod; // Save the matched product object
                break; // Exit the loop once found
            }
        }

        if ($product) {
            $productname = $product->getProductName();
        } else {
            $productname = "No product found with the given ObjKey.";
        }

        $logger->info('product Object', [
            'product' => $productname,
            
        ]);
    
        return $this->render('Professional/LuxuryProductsSinglePage.html.twig', [
            'product' => $product,
            
        ]);
    }




    /**
     * @Route("/Search/{url}", name="Search_page")
     */
    public function SearchAction($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Fetch Profiles objects
        $keyword = $url;
        $keyword= strtolower($keyword);
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
     * @Route("/Search/{profiletype}/{url}", name="ProSearch_page")
     */
    public function ProSearchAction($url, $profiletype, Request $request, PaginatorInterface $paginator)
    {
        // Fetch Profiles objects
        $keyword = urldecode($url);
        $customertype = $profiletype;
        $searchTerms = preg_split('/\s+/', strtolower(trim($keyword)));
        
        $proProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        
        // Only add condition if not searching "All" categories
        if ($customertype !== 'All') {
            $proProfileList->addConditionParam("PortfolioType = ?", $customertype);
        }
        
        $ProProfiles = $proProfileList->load();

        // Score profiles based on search relevance
        $scoredProfiles = array_map(function ($profile) use ($searchTerms, $keyword) {
            $score = 0;
            $companyName = strtolower($profile->getCompanyName());
            $description = strtolower($profile->getDescription());
            $portfolioType = strtolower($profile->getPortfolioType());
            
            // Exact match boost (highest priority)
            if (strtolower($keyword) === $companyName) {
                $score += 100;
            }
            
            // All terms match in company name (high priority)
            if ($this->allTermsMatch($searchTerms, $companyName)) {
                $score += 50;
            }
            
            // All terms match anywhere (medium priority)
            $fullText = "$companyName $description $portfolioType";
            if ($this->allTermsMatch($searchTerms, $fullText)) {
                $score += 30;
            }
            
            // Count matching terms (lower priority)
            $matchedTerms = 0;
            foreach ($searchTerms as $term) {
                if (strpos($companyName, $term) !== false) {
                    $matchedTerms++;
                    $score += 10; // More weight for matches in company name
                } elseif (strpos($description, $term) !== false) {
                    $matchedTerms++;
                    $score += 5;
                } elseif (strpos($portfolioType, $term) !== false) {
                    $matchedTerms++;
                    $score += 3;
                }
            }
            
            // Partial score based on how many terms matched
            if ($matchedTerms > 0) {
                $score += ($matchedTerms / count($searchTerms)) * 20;
            }
            
            return [
                'profile' => $profile,
                'score' => $score,
                'matchedTerms' => $matchedTerms
            ];
        }, $ProProfiles);

        // Filter out zero-score results and sort by score
        $filteredProfiles = array_filter($scoredProfiles, function ($item) {
            return $item['score'] > 0;
        });
        
        usort($filteredProfiles, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Extract just the profiles for pagination
        $sortedProfiles = array_column($filteredProfiles, 'profile');

        // If searching for Architect or All, also search in the Architect database
        $architectsFromDB = [];
        if (strtolower($customertype) === 'architect' || $customertype === 'All') {
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            
            try {
                $pdo = new \PDO($dsn, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Build search conditions for each term
                $conditions = [];
                $params = [];
                foreach ($searchTerms as $i => $term) {
                    $conditions[] = "(LOWER(Name) LIKE :term$i OR 
                                    LOWER(Registered_Architect_No) LIKE :term$i OR 
                                    LOWER(Address) LIKE :term$i OR 
                                    LOWER(Pincode) LIKE :term$i OR 
                                    LOWER(Phone_No) LIKE :term$i OR 
                                    LOWER(Email_Id) LIKE :term$i)";
                    $params[":term$i"] = "%$term%";
                }
                
                $sql = "SELECT * FROM Architect WHERE " . implode(' AND ', $conditions);
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $architectsFromDB = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Handle database connection error gracefully
                $architectsFromDB = [];
            }
        }
        
        // Merge ProProfiles with Architect database results
        $allResults = array_merge($sortedProfiles, $architectsFromDB);
        
        $pagination = $paginator->paginate(
            $allResults,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();

        return $this->render('Professional/Searchlisting.html.twig', [
            'ProProfiles' => $pagination,
            'paginationVariables' => $paginationVariables,
            'Keyword' => $keyword,
            'CustomerType' => $customertype,
        ]);
    }


    // Helper function to check if all search terms appear in the text
    private function allTermsMatch(array $terms, string $text): bool
    {
        foreach ($terms as $term) {
            if (strpos($text, $term) === false) {
                return false;
            }
        }
        return true;
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

            

            $url = $customer->getUserID();
            $customertype = $customer->getcustomertype();
            
            // Check if customertype is empty and redirect to homepage if true
            if (empty($customertype)) {
                return $this->redirect('/'); // Replace 'homepage' with your actual homepage route name
            }

            

            $architectProfile = null;
            $architectActivate = null;

            if ($customer->getEmailVerified() !== 'true') {
                return $this->redirectToRoute('Account-Verification-OTP', ['url' => $url]);
            }
            if ($customer->getPhoneVerified() !== 'True') {
                return $this->redirectToRoute('Account-Verification-OTP', ['url' => $url]);
            }

            $form1 = $this->createForm(SearchFormType::class);
            $form1->handleRequest($request);
            if ($form1->isSubmitted() && $form1->isValid()) {
                $formData1 = $form1->getData();
                $searchKeyword = $form1->get('Search')->getData();
                return $this->redirect("Search/$searchKeyword");
            }

            if ($customertype === 'Admin'){
                $ChannelPartnersList = new \Pimcore\Model\DataObject\Customer\Listing();
                $ChannelPartnersList->addConditionParam("customertype = ?", 'ChannelPartner');
                $channelPartnersCount = $ChannelPartnersList->getTotalCount();

                $ChannelPartners = $ChannelPartnersList->load();

                $AgentsList = new \Pimcore\Model\DataObject\Customer\Listing();
                $AgentsList->addConditionParam("customertype = ?", 'Agent');
                $AgentsListCount = $AgentsList->getTotalCount();

                $Agents = $AgentsList->load();

                return $this->render('account/AdminPartner_dashboard.html.twig', [
                    'customer' => $customer,
                    'ChannelPartners' => $ChannelPartners,
                    'channelPartnersCount' => $channelPartnersCount,
                    'Agents' => $Agents,
                    'AgentsListCount' => $AgentsListCount,
                ]);
            }

            if ($customertype === 'ChannelPartner'){
                $Agents = $customer->getMyAgents();

                return $this->render('account/Channelpartner_dashboard.html.twig', [
                    'customer' => $customer,
                    'Agents' => $Agents,
                ]);
            }

            if ($customertype === 'Agent'){
                $MyCustomers = $customer->getMyCustomers();

                return $this->render('account/Agent_dashboard.html.twig', [
                    'customer' => $customer,
                    'MyCustomers' => $MyCustomers
                ]);
            }

            if ($customertype === 'Customer'){
                return $this->render('account/customer_dashboard.html.twig', [
                    'customer' => $customer,
                ]);
            }
            elseif ($customertype === 'Contractor' || $customertype === 'Professional' || $customertype === 'Supplier' || $customertype === 'Engineer' || $customertype === 'Designer' || $customertype === 'Architect' || $customertype === 'Builder' || $customertype === 'Manufacturer' || $customertype === 'Distributor' || $customertype === 'Retailer' || $customertype === 'Dealer' || $customertype === 'Earth Movers'){
                $PortfolioActivate = $customer->getPortfolioActivate();
                if($PortfolioActivate === 'true'){
                    $subscriptionStart = $customer->getSubscriptionStart();

                    if ($subscriptionStart) {
                        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                        $oneYearAfterSubscription = clone $subscriptionStart;
                        $oneYearAfterSubscription->modify('+1 year');
                        
                        // If current date is after one year of subscription start, show annual fee
                        if ($now >= $oneYearAfterSubscription) {
                            return $this->redirect('/account/pricing');
                        }
                    } else {
                        // First time user, show annual fee
                        return $this->redirect('/account/pricing');
                    }

                    $ProProfiles = $customer->getPortfolio();
                    $ProProfile = $ProProfiles[0];
                    


                    if($ProProfile){
                        $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
                        $NotificationList->addConditionParam("professional = ?", $ProProfile);
            
                        $NotificationList->setOrderKey('creationDate');
                        $NotificationList->setOrder('desc');

                        $ProProjects = $ProProfile->getProjects();
                        $ProProjects  = array_slice($ProProjects, 0, 3);

                        $ProProducts = new \Pimcore\Model\DataObject\ProProduct\Listing();
                        $ProProducts->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProProducts->setOrderKey('creationDate');
                        $ProProducts->setOrder('desc');

                        $ProProducts = $ProProducts->load();
                        

                        $form = $this->createForm(ProRequirementFormType::class);
                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
                            $uploadedFile = $form->get('excelFile')->getData();
                            $proRequirement = new ProRequirement();

                            try {
                                $asset = new Document();
                                $asset->setData(file_get_contents($uploadedFile->getPathname()));
                                $timestamp = time();
                                $originalFilename = $uploadedFile->getClientOriginalName();
                                $newFilename = $timestamp . '_' . $originalFilename;
                                $asset->setFilename($newFilename);

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
            
                                $this->addFlash('success', $translator->trans('Requirements submitted succesfully.'));
                            } catch (FileException $e) {
                                // Handle file upload error
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

                        $activeRequirements = [];
                        $expiredRequirements = [];

                        foreach ($ProRequirements as $ProRequirement) {
                            if ($ProRequirement->getExpiryCheck() === 'Active') {
                                $activeRequirements[] = $ProRequirement;
                            } elseif ($ProRequirement->getExpiryCheck() === 'Expired') {
                                $expiredRequirements[] = $ProRequirement;
                            }
                        }
                        
                        $ProEnquiryList = new \Pimcore\Model\DataObject\ProEnquiry\Listing();
                        $ProEnquiryList->addConditionParam("ProfessionalPath = ?", $ProProfile);

                        $ProEnquiryList->setOrderKey('creationDate');
                        $ProEnquiryList->setOrder('desc');

                        $ProEnquiries = $ProEnquiryList->load();
                        $ProEnquiries = array_reverse($ProEnquiries);

                        $Endorsements = $customer->getEndorsement();
                        $numberOfEndorsements = count($Endorsements);

                        $ProEndorsements = $Endorsements;
                    }

                    $ExpiringBOQCount = null;
                    $AwaitingBOQCount = null;

                    if ($customertype === 'Manufacturer' || $customertype === 'Distributor' || $customertype === 'Retailer' || $customertype === 'Dealer' || $customertype === 'Supplier'){
                        $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            
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
            
                        $customerProducts = $ProProfile->getProducts();
                        $customerTags = [];
            
                        foreach ($customerProducts as $product) {
                            $tags = $product->getTags();
                            $customerTags = array_merge($customerTags, array_map('trim', explode(',', $tags)));
                        }
            
                        $enabledRequirements = [];
                        $disabledRequirements = [];
            
                        foreach ($activeRequirements as $requirement) {
                            $enabled = false;
            
                            foreach ($requirement->getProRequirementProduct() as $product) {
                                if (in_array($product->getProductName(), $customerTags, true)) {
                                    $enabled = true;
                                    break;
                                }
                            }
            
                            if ($enabled) {
                                $enabledRequirements[] = $requirement;
                            } else {
                                $disabledRequirements[] = $requirement;
                            }
                        }

                        $expiringBOQs = [];
                        $currentDate = new \DateTime();

                        foreach ($enabledRequirements as $enabledRequirement) {
                            $expiryDate = $enabledRequirement->getExpireDate();

                            if ($expiryDate instanceof \DateTime) {
                                $daysLeft = $currentDate->diff($expiryDate)->days;

                                if ($daysLeft <= 2 && $expiryDate > $currentDate) {
                                    $expiringBOQs[] = $enabledRequirement;
                                }
                            }
                        }

                        $ExpiringBOQCount = count($expiringBOQs);
                        $AwaitingBOQCount = count($enabledRequirements);
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
                        'activeRequirements' => $activeRequirements,
                        'expiredRequirements' => $expiredRequirements,   
                        'ExpiringBOQCount' => $ExpiringBOQCount,  
                        'AwaitingBOQCount' => $AwaitingBOQCount,                
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
    public function generateQuote(Request $request, Security $security, LoggerInterface $logger): Response
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            $customer = $user;
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }

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
            $OrgCity = $selectedRequirement->getCity();

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

            $preset_date = (new \DateTime())->format('M j, Y');
            $future_date = (new \DateTime())->modify('+15 days')->format('M j, Y');
            
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
                                        <div>Quote No #: A00364</div>
                                        <div>Quote Date #: '.$preset_date.'</div>
                                        <div>Valid Till: '.$future_date.'</div>
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
                                                <div><b>Phone:</b> +91 9150202745</div>
                                                
                                            </div>
                                        </div>
                                    </td>
                                    <td class="billedto">
                                        <div class="toaddress">
                                            <h2>Billed To</h2>
                                            <div><b>'.$ProProfile->getCompanyName().'</b></div>
                                                <div class="companyinfo" style="font-size:12px;">
                                                    <div>'.$ProProfile->getStreetAddress().', '.$ProProfile->getCity().', '.$ProProfile->getState().', '.$ProProfile->getCountry().' - '.$ProProfile->getPinCode().'</div>
                                                    <div><b>GSTIN:</b> '.$ProProfile->getgstnumber().'</div>
                                                    <div><b>Phone:</b>+91 '.$ProProfile->getPhoneNumber().'</div>
                                                </div>
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
                $OrgUnit = $product->getUnit();
                $OrgType = $product->getProdType();

                $logger->info('Processing product', [
                    'productName' => $productName,
                    'brand' => $brand,
                    'material' => $material,
                    'quantity' => $quantity
                ]);


                
                $sql = "SELECT Unique_ID FROM products
                    WHERE Product_Name LIKE :productName
                    AND Product_Brand = :brand";
                    

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productName', '%' . $productName . '%');
                $stmt->bindValue(':brand', $brand);
            
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
                $logger->info('Unit price and unit before ChatGPT', ['unitPrice' => $unitPrice]);

                $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
                $apiKey = $openaiConfig['api_key'];
                $logger->info('Checking condition for unitPrice', ['unitPrice' => $unitPrice]);


                if (trim($unitPrice) === 'N/A') {
                    $logger->info('Calling ChatGPT for unit price prediction', [
                        'productName' => $productName,
                        'brand' => $brand
                    ]);
                
                    // Prepare the prompt
                    $prompt = <<<PROMPT
                    Act as an expert price predictor in the construction industry who knows the exact price of construction products per Unit. You will act like a calculator and only respond in the following format: 'Price: XXXRs, Unit: XX' (e.g., 'Price: 3500Rs, Unit: Kg (The Unit Should be Same as the Unit Given in the Prompt below)'). The Price Should be Per Unit Given. If you don't have the exact price, predict the price based on the city and other details provided.
                    
                    Below is the product name and city:
                    {
                    Product Name: [$productName],
                    Unit: [$OrgUnit],
                    Brand Name: [$brand],
                    Type: [$OrgType],
                    City: [$OrgCity]
                    }
                    PROMPT;

                    // Call OpenAI API
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: Bearer $apiKey",
                        
                    ]);

                    // Prepare the request payload
                    $data = [
                        "model" => "gpt-4o-mini", // Replace with the model you're using
                        "messages" => [
                            ["role" => "user", "content" => $prompt]
                        ],
                        "temperature" => 0.7,
                        "max_tokens" => 50
                    ];

                    $logger->info('Sending to ChatGPT API', [
                        'url' => "https://api.openai.com/v1/chat/completions",
                        'headers' => [
                            "Content-Type: application/json",
                            "Authorization: Bearer $apiKey"
                        ],
                        'payload' => json_encode($data)
                    ]);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    $response = curl_exec($ch);

                    

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $logger->info('HTTP Status Code', ['code' => $httpCode]);
                    $logger->info('HTTP Response ChatGPT', ['code' => $response]);

                    if ($httpCode !== 200) {
                        error_log("Non-200 HTTP Response: $httpCode");
                    }
                    
                    
                    if ($response === false) {
                        $error = curl_error($ch);
                        error_log("CURL Error: $error");
                    } else {
                        error_log("API Response: $response");
                    }
                    curl_close($ch);

                    // Decode the response
                    $responseData = json_decode($response, true);

                    if (isset($responseData['choices'][0]['message']['content'])) {
                        $apiResponse = $responseData['choices'][0]['message']['content'];

                        $logger->info("ChatGPT Response Content", ['content' => $apiResponse]);

                        // Extract Price and Unit using regular expressions
                        if (preg_match('/Price:\s*(\d+)Rs,\s*Unit:\s*(\w+)/', $apiResponse, $matches)) {
                            $unitPrice = $matches[1]; // Example: 60Rs
                            $unit = $matches[2]; // Example: Sq.ft
                        } else {
                            // Handle invalid response
                            $unitPrice = 'N/A';
                            $unit = 'N/A';
                            error_log("Failed to extract price and unit from OpenAI response: $apiResponse");
                        }
                        $logger->info('Chat GPT Price Output', [
                            'productName' => $productName,
                            'brand' => $brand,
                            'unit price' => $unitPrice,
                            'Unit' => $unit
                        ]);
                    }
                }
                

                

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
                            <td style="text-align:left;">' . $productName .'-'. $OrgType .'</td>
                            <td style="text-align:left;">' . $brand . '</td>
                            
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

        return $this->redirectToRoute('account-login');
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
             // Sort products based on the latest bid date
            usort($ProRequirementProducts, function($a, $b) {
                $latestBidA = $a->getSupplierBid() ? max(array_map(function($bid) { return $bid->getCreationDate(); }, $a->getSupplierBid())) : null;
                $latestBidB = $b->getSupplierBid() ? max(array_map(function($bid) { return $bid->getCreationDate(); }, $b->getSupplierBid())) : null;

                return $latestBidB <=> $latestBidA; // Sort in descending order
            });

            // usort($ProRequirementProducts, function($a, $b) {
            //     return $b->getCreationDate() <=> $a->getCreationDate();
            // });

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
     * @Route("/get-bid-details", name="get_bid_details")
     */
    public function getBidDetailsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $bidKey = $data['bidKey'] ?? null;

        if (!$bidKey) {
            return new JsonResponse(['success' => false, 'message' => 'Bid key is required']);
        }

        try {
            // Get the SupplierBid object using the bidKey
            
            $supplierBid = \Pimcore\Model\DataObject\SupplierBid::getByPath("/SupplierBid/$bidKey");
            
            if (!$supplierBid) {
                return new JsonResponse(['success' => false, 'message' => 'Bid not found']);
            }

            // Extract bid details
            $bidDetails = [
                'success' => true,
                'bidAmount' => $supplierBid->getBidAmount() ? '₹ ' . number_format($supplierBid->getBidAmount()) : '-',
                'deliveryDuration' => $supplierBid->getTimeDuration() ? $supplierBid->getTimeDuration() . ' days' : '-',
                'warrantyPeriod' => $supplierBid->getBidWarrantyPeriod() ? $supplierBid->getBidWarrantyPeriod() . ' months' : '-',
                'paymentTerms' => $supplierBid->getBidPaymentTerms() ?: '-'
            ];

            return new JsonResponse($bidDetails);
            
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Error fetching bid details: ' . $e->getMessage()]);
        }
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
            // $proRequirementProduct->setQuoteStatus('Started');
            $proRequirementProduct->setQuantity($productQuantity);
            $proRequirementProduct->setBrand($brandName);
            $proRequirementProduct->setMaterial($ProductMaterial);
            $proRequirementProduct->save();
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
                    $ProfileType = $supplier->getPortfolioType();
                    $ProfileUrl = '/'.strtolower($ProfileType).'/portfolio/'.$supplier->getKey();



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

            $existingBid = null;
            $ProProfile = null;

            $ProRequirementProductKey = $request->request->get('productKey');
            $ProRequirementProducts = new \Pimcore\Model\DataObject\ProRequirementProduct\Listing();
            $ProRequirementProducts->addConditionParam("ObjKey = ?", $ProRequirementProductKey);

            $RequirementProducts = $ProRequirementProducts->load();
            $ProRequirementProduct = $RequirementProducts[0];

            $ProRequirement = $ProRequirementProduct->getProRequirement();
            $Professional = $ProRequirement->getProfessional();
            $ownerCustomer = $Professional->getCustomer();

            $supplierBids = $ProRequirementProduct->getSupplierBid();

            $customer = $user;
            $customertype = $customer->getcustomertype();
            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }


            // Loop through the existing SupplierBids
            foreach ($supplierBids as $supplierBid) {
                if ($supplierBid->getSupplier()->getKey() === $ProProfile->getKey()) {
                    $existingBid = $supplierBid;
                    break; // Exit the loop as we found a match
                }
            }


            if ($existingBid) {
                $bidAmount = $request->request->get('bidAmount');
                $expiryDate = $request->request->get('deliveryTime');
                $TimeDuration = $request->request->get('timeDuration');
                $WarrantyPeriod = $request->request->get('bidWarrantyPeriod');
                $PaymentTerms = $request->request->get('bidPaymentTerms');
                if ($expiryDate) {
                    try {
                        $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                        $expiryCarbonDate = Carbon::instance($expiryDateTime);
                        $existingBid->setEndDate($expiryCarbonDate);
                    } catch (\Exception $e) {
                        error_log("Error converting expiry date: " . $e->getMessage());
                        // Handle the error, maybe log it or return an error response
                        // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
                    }
                }

                $existingBid->setBidAmount($bidAmount);
                $existingBid->settimeDuration($TimeDuration);
                $existingBid->setbidWarrantyPeriod($WarrantyPeriod);
                $existingBid->setbidPaymentTerms($PaymentTerms);
                $existingBid->save(); 

                $Notification = new ProNotification();
                $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
                $NotrandomNumber = rand(1000, 9999); 
                $uniqueKey = $NotrandomNumber . '-' . time();
                $Notification->setKey(Text::toUrl($uniqueKey));
                $Notification->setMessage("Quote Modified on your BOQ");
                $Notification->setDescription("Click to view Quote");
                $Notification->setCustomer($ownerCustomer);
                $redirecturl = '/BOQ/customize/'.$ProRequirement->getKey();
                $Notification->seturl($redirecturl);
                $Notification->setPublished(true);
                $Notification->save();
            }

            else{
                $productName = $request->request->get('productName');
                $productBrand = $request->request->get('productBrand');
                $productQuantity = $request->request->get('productQuantity');
                $productUnit = $request->request->get('productUnit');
                $productMaterial = $request->request->get('productMeterial');
                $bidAmount = $request->request->get('bidAmount');

                $TimeDuration = $request->request->get('timeDuration');
                $WarrantyPeriod = $request->request->get('bidWarrantyPeriod');
                $PaymentTerms = $request->request->get('bidPaymentTerms');
                
                // $supplierPinnedNotificationPath = $request->request->get('SupplierPinnedNotificationPath');
                $expiryDate = $request->request->get('deliveryTime');
                

                

                // $supplierPinnedNotification = SupplierPinnedNotification::getByPath($supplierPinnedNotificationPath);

                

                

                if ($ProProfiles) {
                        
                    // Create a new SupplierPinnedNotification for each matching tag
                    $supplierBid = new SupplierBid();
                    $supplierBid->setProductName($productName);
                    $supplierBid->setProductBrand($productBrand);
                    $supplierBid->setProductQuantity($productQuantity);
                    $supplierBid->setProductUnit($productUnit);
                    $supplierBid->setMaterial($productMaterial);
                    $supplierBid->setBidAmount($bidAmount);

                    $supplierBid->settimeDuration($TimeDuration);
                    $supplierBid->setbidWarrantyPeriod($WarrantyPeriod);
                    $supplierBid->setbidPaymentTerms($PaymentTerms);

                    // $supplierBid->setSupplierPinnedNotification($supplierPinnedNotification);
                    $supplierBid->setProRequirementProduct($ProRequirementProduct);
                    $supplierBid->setSupplier($ProProfile); // Set the Supplier field to the current ProProfile
                

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

                    
                    


                    $Notification = new ProNotification();
                    $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
                    $NotrandomNumber = rand(1000, 9999); 
                    $uniqueKey = $NotrandomNumber . '-' . time();
                    $Notification->setKey(Text::toUrl($uniqueKey));
                    $Notification->setMessage("You Have Recieved a New Quote on your BOQ");
                    $Notification->setDescription("Click to view Quote");
                    $Notification->setCustomer($ownerCustomer);
                    $redirecturl = '/BOQ/customize/'.$ProRequirement->getKey();
                    $Notification->seturl($redirecturl);
                    $Notification->setPublished(true);
                    $Notification->save();



                    $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                    $EmailTemplates->addConditionParam("TemplateName = ?", "QuoteReceivedEmail");
                    $EmailTemplate = $EmailTemplates->load();
                    $EmailTemplate = $EmailTemplate[0];

                    $subject = $EmailTemplate->getSubject();
                    $htmlContent = $EmailTemplate->getContent();
                    eval("\$htmlContent = \"$htmlContent\";");
                    // Create a new Pimcore\Mail instance
                    $mail = new \Pimcore\Mail();
                    // $mail->from('arqonztest@gmail.com');
                    $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                    $mail->to($ownerCustomer->getEmail());
                    $mail->subject($subject);
                    $mail->html($htmlContent);
                    $mail->send();

                    // $supplierPinnedNotification->setStatus('Accepted');
                    // $supplierPinnedNotification->setCurrentBid($supplierBid);
                    // $supplierPinnedNotification->save();
                    

                }

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
     * @Route("/search/suggestions", name="search_suggestions", methods={"POST"})
     */
    public function getSearchSuggestions(Request $request): JsonResponse
    {
        $searchTerm = $request->request->get('searchTerm', '');
        $category = $request->request->get('category', 'Architect');
        
        // Return default suggestions when search term is empty
        if (empty($searchTerm)) {
            $defaultSuggestions = [
                "Best $category in Chennai",
                "Top $category in Delhi",
                "$category near me",
                "Affordable $category",
                "Professional $category services"
            ];
            return new JsonResponse($defaultSuggestions);
        }

        $profileListing = new Listing();
        $profileListing->addConditionParam("PortfolioType = ?", $category);
        $profileListing->addConditionParam("CompanyName LIKE ?", "%" . $searchTerm . "%");
        $profileListing->setLimit(10);
        
        $profiles = $profileListing->load();
        
        $suggestions = [];
        foreach ($profiles as $profile) {
            $suggestions[] = $profile->getCompanyName();
        }

        return new JsonResponse($suggestions);
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
     * @Route("/about-us", name="About Us Page")
     */
    public function AboutUsAction( Request $request, PaginatorInterface $paginator)
    {   
        return $this->render('Professional/about-us-page.html.twig', [
            
        ]);
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

            $Firstname = $customer->getfirstname();
            $Lastname = $customer->getlastname();
            $Email = $customer->getemail();
            $Phone = $customer->getphone();
            $Street = $customer->getstreet();
            $City = $customer->getcity();
            $dob = $customer->getDOB();
            $state = $customer->getState();
            $country = $customer->getCountry();
            $zipcode = $customer->getzip();


            

            return $this->render('Professional/dashboard_Profile.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile,
                'Firstname' => $Firstname,
                'Lastname' => $Lastname,
                'Email' => $Email,
                'DOBv' => $dob,
                'City' => $City,
                'Street' => $Street,
                'state' => $state,
                'country' => $country,
                'zipcode' => $zipcode,
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
                    $DOB = new \DateTime($DOB);
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
                    $customer->setzip($Pincode);
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
            if($customer->getEducation() === null) {
                $Education = 'Ex. M.Arch, Harward University';
            }else {
                $Education = $customer->getEducation();
            }

            if($customer->getSpecialization() === null) {
                $Specialization = 'Ex. Commercial Architecture';
            }else {
                $Specialization = $customer->getSpecialization();
            }

            if($customer->getAwards() === null) {
                $Awards = 'Ex. AIA Young Architects Award, Best Commer';
            }else {
                $Awards = $customer->getAwards();
            }

            if($customer->getCertificationAndLicences() === null) {
                $Certification = 'Ex. LEED Accredited Professional';
            }else {
                $Certification = $customer->getCertificationAndLicences();
            }



            return $this->render('Professional/dashboard_Profile_Professional.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile,
                'Education' => $Education,
                'Specialization' => $Specialization,
                'Awards' => $Awards,
                'Certification' => $Certification,
                

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
                    $customer->setCertificationAndLicences($Certification);
                }

                // Save the ProProfile object only if changes were made
                $customer->save();
            }
        }

        return new Response('Profile details saved successfully.', Response::HTTP_OK);
    }

    /**
     * @Route("/account/profile-picture/upload", name="account_profile_picture_upload", methods={"POST"})
     */
    public function uploadProfilePictureAction(Request $request, Security $security): JsonResponse
    {
        try {
            // Check if user is authenticated
        $user = $security->getUser();
        

            // Check if file was uploaded
            if (!$request->files->has('profilePicture')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
        }

        $uploadedFile = $request->files->get('profilePicture');

            // Validate file
            if (!$uploadedFile->isValid()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 400);
            }

            // Validate file type
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($uploadedFile->getMimeType(), $allowedMimeTypes)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Only JPEG, PNG, GIF, and WebP images are allowed'
                ], 400);
            }

            // Validate file size (max 5MB)
            if ($uploadedFile->getSize() > 5 * 1024 * 1024) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'File size must be less than 5MB'
                ], 400);
            }

            // Create asset for the profile picture
            $parentFolder = Asset\Folder::getByPath('/user-profile-pictures');
            if (!$parentFolder) {
                $parentFolder = new Asset\Folder();
                $parentFolder->setParent(Asset::getById(1));
                $parentFolder->setFilename('user-profile-pictures');
                $parentFolder->save();
            }

            // Generate unique filename
            $filename = 'profile-picture-' . $user->getId() . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            // Create and save the asset
            $asset = new Asset\Image();
            $asset->setParent($parentFolder);
            $asset->setFilename($filename);
            $asset->setData(file_get_contents($uploadedFile->getPathname()));
            $asset->save();

            // Update user's profile picture reference
            $user->setProfilePicture($asset);
            $user->save();

            // Generate thumbnail URL
            $thumbnail = $asset->getThumbnail('profile');
            $thumbnailUrl = $thumbnail->getPath();

            return new JsonResponse([
                'success' => true,
                'message' => 'Profile picture uploaded successfully',
                'thumbnailUrl' => $thumbnailUrl,
                'assetId' => $asset->getId()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error uploading profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/account/profile-picture/delete", name="account_profile_picture_delete", methods={"POST"})
     */
    public function deleteProfilePictureAction(Request $request, Security $security): JsonResponse
    {
        try {
            $user = $security->getUser();

            // Remove profile picture reference
            $user->setProfilePicture(null);
            $user->save();

            return new JsonResponse([
                'success' => true,
                'message' => 'Profile picture removed successfully'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error removing profile picture: ' . $e->getMessage()
            ], 500);
        }
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

                if($ProProfile->getCompanyName() === null) {
                    $CompanyName = 'Ex. ABC pvt Ltd.';
                }else {
                    $CompanyName = $ProProfile->getCompanyName();
                }

                if($ProProfile->getGSTNumber() === null) {
                    $GSTNumber = 'Ex. 29GGGGG1314R9Z6';
                }else {
                    $GSTNumber = $ProProfile->getGSTNumber();
                }

                if($ProProfile->getDescription() === null) {
                    $Description = 'Write a description about your company.';
                }else {
                    $Description = $ProProfile->getDescription();
                }

                if($ProProfile->getSkills() === null) {
                    $Skills = 'Ex. 3D Designing, Building Layout';
                }else {
                    $Skills = $ProProfile->getSkills();
                }

                if($ProProfile->getSpecialization() === null) {
                    $Specialization = 'Ex. Farmhouse, SkyScrapper';
                }else {
                    $Specialization = $ProProfile->getSpecialization();
                }

                if($ProProfile->getCitiesServed() === null) {
                    $CitiesServed = 'Ex. New Delhi, Chennai, Kolkata';
                }else {
                    $CitiesServed = $ProProfile->getCitiesServed();
                }

                if($ProProfile->getYearEstablished() === null) {
                    $YearEstablished = 'Ex. 2019';
                }else {
                    $YearEstablished = $ProProfile->getYearEstablished();
                }

                if($ProProfile->getPriceForHour() === null) {
                    $PriceForHour = 'Ex. 20000';
                }else {
                    $PriceForHour = $ProProfile->getPriceForHour();
                }

                if($ProProfile->getStreetAddress() === null) {
                    $StreetAddress = 'Street';
                }else {
                    $StreetAddress = $ProProfile->getStreetAddress();
                }

                if($ProProfile->getCity() === null) {
                    $City = 'City';
                }else {
                    $City = $ProProfile->getCity();
                }
                

                if($ProProfile->getState() === null) {
                    $State = 'State';
                }else {
                    $State = $ProProfile->getState();
                }

                if($ProProfile->getCountry() === null) {
                    $Country = 'Country';
                }else {
                    $Country = $ProProfile->getCountry();
                }

                if($ProProfile->getPinCode() === null) {
                    $PinCode = 'Pin Code';
                }else {
                    $PinCode = $ProProfile->getPinCode();
                }

                if($ProProfile->getCompanyWebsite() === null) {
                    $CompanyWebsite = 'Company Website';
                }else {
                    $CompanyWebsite = $ProProfile->getCompanyWebsite();
                }

                if($ProProfile->getCoaNumber() === null) {
                    $CoaNumber = 'COA Number';
                }else {
                    $CoaNumber = $ProProfile->getCoaNumber();
                }

    
            }
            



            return $this->render('Professional/dashboard_Profile_Company.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile,
                'CompanyName' => $CompanyName,
                'GSTNumber' => $GSTNumber,
                'Description' => $Description,
                'Skills' => $Skills,
                'Specialization' => $Specialization,
                'CitiesServed' => $CitiesServed,
                'YearEstablished' => $YearEstablished,
                'PriceForHour' => $PriceForHour,
                'StreetAddress' => $StreetAddress,
                'City' => $City,
                'State' => $State,
                'Country' => $Country,
                'PinCode' => $PinCode,
                'CompanyWebsite' => $CompanyWebsite,
                'CoaNumber' => $CoaNumber,

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

            if($customer->getLinkedIn() === null) {
                $LinkedIn = 'LinkedIn URL';
            }else {
                $LinkedIn = $customer->getLinkedIn();
            }

            if($customer->getFacebook() === null) {
                $Facebook = 'Facebook URL';
            }else {
                $Facebook = $customer->getFacebook();
            }

            if($customer->getTwitter() === null) {
                $Twitter = 'Twitter URL';
            }else {
                $Twitter = $customer->getTwitter();
            }

            if($customer->getInstagram() === null) {
                $Instagram = 'Instagram handle';
            }else {
                $Instagram = $customer->getInstagram();
            }



            return $this->render('Professional/dashboard_socialmedia_profile.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile,
                'LinkedIn' => $LinkedIn,
                'Facebook' => $Facebook,
                'Twitter' => $Twitter,
                'Instagram' => $Instagram,
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
            // $subscriptionStart = $customer->getSubscriptionStart();

            // if ($subscriptionStart) {
            //     $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            //     $oneYearAfterSubscription = clone $subscriptionStart;
            //     $oneYearAfterSubscription->modify('+1 year');
                
            //     // If current date is after one year of subscription start, show annual fee
            //     if ($now >= $oneYearAfterSubscription) {
            //         return $this->redirect('/account/pricing');
            //     }
            // } else {
            //     // First time user, show annual fee
            //     return $this->redirect('/account/pricing');
            // }

            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $MainCustomerType = $ProProfile->getPortfolioType();
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->setOrderKey('creationDate');
            $NotificationList->setOrder('desc');

            $ProProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
            $ProProjectsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            // Handle sorting
            $sort = $request->query->get('sort', 'date-desc');
            switch ($sort) {
                case 'date-asc':
            $ProProjectsList->setOrderKey('creationDate');
                    $ProProjectsList->setOrder('asc');
                    break;
                case 'name-asc':
                    $ProProjectsList->setOrderKey('ProjectName');
                    $ProProjectsList->setOrder('asc');
                    break;
                case 'name-desc':
                    $ProProjectsList->setOrderKey('ProjectName');
            $ProProjectsList->setOrder('desc');
                    break;
                case 'location-asc':
                    $ProProjectsList->setOrderKey('Location');
                    $ProProjectsList->setOrder('asc');
                    break;
                case 'location-desc':
                    $ProProjectsList->setOrderKey('Location');
                    $ProProjectsList->setOrder('desc');
                    break;
                default: // date-desc
                    $ProProjectsList->setOrderKey('creationDate');
                    $ProProjectsList->setOrder('desc');
            }

            $ProProjects = $ProProjectsList->load();


            return $this->render('Professional/dashboard_Projects.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProProjects' => $ProProjects,
                    'MaincustType' => $MainCustomerType,
                    'currentSort' => $sort
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    
    /**
     * @Route("/account/BOQ-List", name="account-BOQ-List")
     */
    public function DashboardBOQListAction(Request $request, Security $security)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0]; // Assuming first profile is used

            // Load all active ProRequirements
            $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
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

            // Fetch user tags
            $customerProducts = $ProProfile->getProducts();
            $customerTags = [];

            foreach ($customerProducts as $product) {
                $tags = $product->getTags(); // Expecting comma-separated string
                if (is_string($tags)) {
                    $tagArray = array_map('strtolower', array_map('trim', explode(',', $tags)));
                    $customerTags = array_merge($customerTags, $tagArray);
                }
            }

            $enabledRequirements = [];
            $disabledRequirements = [];

            foreach ($activeRequirements as $requirement) {
                $enabled = false;

                foreach ($requirement->getProRequirementProduct() as $product) {
                    $productName = strtolower(trim($product->getProductName()));
                    if (in_array($productName, $customerTags, true)) {
                        $enabled = true;
                        break;
                    }
                }

                if ($enabled) {
                    $enabledRequirements[] = $requirement;
                } else {
                    $disabledRequirements[] = $requirement;
                }
            }

            return $this->render('Professional/dashboard_BOQ_listing.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'ProRequirements' => $activeRequirements,
                'customerTags' => $customerTags,
                'enabledRequirements' => $enabledRequirements,
                'disabledRequirements' => $disabledRequirements,
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/account/Products", name="account-Products-list")
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

            // Handle sorting
            $sort = $request->query->get('sort', 'date-desc');
            switch ($sort) {
                case 'name-asc':
                    $ProProductsList->setOrderKey('ProductName');
                    $ProProductsList->setOrder('asc');
                    break;
                case 'name-desc':
                    $ProProductsList->setOrderKey('ProductName');
                    $ProProductsList->setOrder('desc');
                    break;
                case 'date-asc':
                    $ProProductsList->setOrderKey('creationDate');
                    $ProProductsList->setOrder('asc');
                    break;
                case 'price-asc':
                    $ProProductsList->setOrderKey('Price');
                    $ProProductsList->setOrder('asc');
                    break;
                case 'price-desc':
                    $ProProductsList->setOrderKey('Price');
                    $ProProductsList->setOrder('desc');
                    break;
                case 'views-desc':
                    $ProProductsList->setOrderKey('pageViewsCount');
                    $ProProductsList->setOrder('desc');
                    break;
                case 'views-asc':
                    $ProProductsList->setOrderKey('pageViewsCount');
                    $ProProductsList->setOrder('asc');
                    break;
                default: // date-desc
                    $ProProductsList->setOrderKey('creationDate');
                    $ProProductsList->setOrder('desc');
            }

            $ProProducts = $ProProductsList->load();

            return $this->render('Professional/dashboard_products_list.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'customertype' => $customertype,
                'ProProducts' => $ProProducts,
                'currentSort' => $sort
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * @Route("/terms-and-conditions", name="terms and conditions")
     */
    public function TermsofuseAction( Request $request, PaginatorInterface $paginator)
    {
        
        return $this->render('Professional/terms-of-use.html.twig', [
        ]);
    }



    /**
     * @Route("/privacy-policy", name="Privacy-Policy")
     */
    public function PrivacyPolicyAction( Request $request, PaginatorInterface $paginator)
    {
        

        return $this->render('Professional/privacy-policy-page.html.twig', [
        ]);
    }


    /**
     * @Route("/user-data-deletion", name="data-Policy")
     */
    public function DataDeletionAction( Request $request, PaginatorInterface $paginator)
    {
        

        return $this->render('Professional/data-deletion-page.html.twig', [
        ]);
    }



    /**
     * @Route("/contact-us", name="Contact-Us")
     */
    public function ContactUsAction( Request $request, PaginatorInterface $paginator)
    {
        

        return $this->render('Professional/contact-us-page.html.twig', [
        ]);
    }


    /**
     * @Route("/submit-contact-form", name="submit_contact_form", methods={"POST"})
     */
    public function submitContactForm(Request $request): JsonResponse
    {
        // Get form data
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $company = $request->request->get('company');
        $message = $request->request->get('message');

        // Create new ContactForm object and set data
        $contactForm = new ContactForm();
        $contactForm->setName($name);
        $contactForm->setEmail($email);
        $contactForm->setPhone($phone);
        $contactForm->setCompany($company);
        $contactForm->setMessage($message);

        // Save the object to a specific folder
        try {
            $contactForm->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/ContactForms'));
            $contactForm->setKey(uniqid());
            $contactForm->setPublished(true);
            $contactForm->save();
            
            // Return success response
            return new JsonResponse(['success' => true, 'message' => 'Your message has been submitted successfully.']);
        } catch (\Exception $e) {
            // Log the error and return failure response
            error_log("Error saving contact form: " . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'There was an error submitting your message.']);
        }
    }



    /**
     * @Route("/account/Project/edit/{url}", name="account-Project-edit")
     */
    public function DashboardProjectEditAction($url, Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $customertype = $ProProfile->getPortfolioType();
            
            // Determine the form type and project path based on customer type
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
            
            // Get existing images for preview
            $existingImages = [];
            $existingVideos = [];
            
            $gallery = $ProProject->getProjectGallery();
            if ($gallery instanceof ImageGallery) {
                foreach ($gallery->getItems() as $item) {
                    if ($item instanceof Hotspotimage) {
                        $image = $item->getImage();
                        if ($image instanceof Image) {
                            $existingImages[] = [
                                'id' => $image->getId(),
                                'path' => $image->getFullPath(),
                                'thumbnail' => $image->getThumbnail('gallery_preview')
                            ];
                        }
                    }
                }
            }
            
            // Get existing videos
            $videoPaths = $ProProject->getProjectVideoPaths();
            if (!empty($videoPaths)) {
                $videoArray = explode('|', $videoPaths);
                foreach ($videoArray as $videoPath) {
                    if (!empty($videoPath)) {
                        $existingVideos[] = $videoPath;
                    }
                }
            }
            
            // Populate form fields
            foreach ($form->all() as $formField) {
                $fieldName = $formField->getName();
        
                // Exclude ProjectGallery field
                if ($fieldName !== 'ProjectGallery' && $fieldName !== '_submit' && $fieldName !== 'FloorMaps' && $fieldName !== 'ReraApproval') {
                    $formField->setData($ProProject->{'get' . ucfirst($fieldName)}());
                } elseif ($fieldName === 'ReraApproval') {
                    $reraApprovalValue = $ProProject->getReraApproval();
                    $reraApprovalBoolean = $reraApprovalValue === '1';
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
                
                // Handle deleted images from request - FIXED: More robust approach
                $deletedImages = $request->request->get('deleted_images', '');
                if (!empty($deletedImages)) {
                    // Ensure we always have an array
                    if (is_array($deletedImages)) {
                        $deletedImageIds = $deletedImages;
                    } else {
                        $deletedImageIds = explode(',', $deletedImages);
                    }
                    
                    // Remove empty values
                    $deletedImageIds = array_filter($deletedImageIds);
                    
                    foreach ($deletedImageIds as $imageId) {
                        if (!empty($imageId)) {
                            $image = Image::getById($imageId);
                            if ($image instanceof Image) {
                                $image->delete();
                            }
                        }
                    }
                } else {
                    $deletedImageIds = [];
                }
                
                // Handle deleted videos from request - FIXED: More robust approach  
                $deletedVideos = $request->request->get('deleted_videos', '');
                if (!empty($deletedVideos)) {
                    // Ensure we always have an array
                    if (is_array($deletedVideos)) {
                        $deletedVideoPaths = $deletedVideos;
                    } else {
                        $deletedVideoPaths = explode(',', $deletedVideos);
                    }
                    
                    // Remove empty values
                    $deletedVideoPaths = array_filter($deletedVideoPaths);
                } else {
                    $deletedVideoPaths = [];
                }
                
                // Handle new gallery uploads
                $galleryData = $form->get('ProjectGallery')->getData();
                $items = [];
                $videoPaths = [];
                
                // Add existing images that weren't deleted - FIXED: Use correct variable
                if ($gallery instanceof ImageGallery) {
                    foreach ($gallery->getItems() as $item) {
                        if ($item instanceof Hotspotimage) {
                            $image = $item->getImage();
                            if ($image instanceof Image && !in_array($image->getId(), $deletedImageIds)) {
                                $items[] = $item;
                            }
                        }
                    }
                }
                
                // Add existing videos that weren't deleted - FIXED: Use correct variable
                $existingVideoPaths = $ProProject->getProjectVideoPaths();
                if (!empty($existingVideoPaths)) {
                    $existingVideosArray = explode('|', $existingVideoPaths);
                    foreach ($existingVideosArray as $videoPath) {
                        if (!empty($videoPath) && !in_array($videoPath, $deletedVideoPaths)) {
                            $videoPaths[] = $videoPath;
                        }
                    }
                }
                
                // Process new uploads
                if ($galleryData) {
                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                
                                // Set parent based on customer type
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
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }
                }
                
                // Set image gallery
                if (!empty($items)) {
                    $ProProject->setProjectGallery(new ImageGallery($items));
                } else {
                    $ProProject->setProjectGallery(null);
                }
                
                // Set video paths (using pipe delimiter)
                if (!empty($videoPaths)) {
                    $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                } else {
                    $ProProject->setProjectVideoPaths(null);
                }
                
                // Handle floor maps for builders
                if($customertype === 'Builder'){
                    $FloormapData = $form->get('FloorMaps')->getData();
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
                        $videoPaths = [];
                        $hotspotImages = [];

                        foreach ($FloormapData as $file) {
                            $hotspotImage = new Hotspotimage();
                            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
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
                
                $ProProject->setPublished(true);    
                $ProProject->save();
                $this->addFlash('success', $translator->trans('Project Updated successfully.'));
                return $this->redirectToRoute('account-Projects');
            }

            return $this->render('Professional/dashboard_project_edit.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'ProProject' => $ProProject,
                'form' => $form->createView(),
                'existingImages' => $existingImages,
                'existingVideos' => $existingVideos
            ]);
        }
        
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
    public function DashboardRequirementsAction(Request $request, Security $security, Translator $translator, LoggerInterface $logger)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            $subscriptionStart = $customer->getSubscriptionStart();

            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    return $this->redirect('/account/pricing');
                }
            } else {
                // First time user, show annual fee
                return $this->redirect('/account/pricing');
            }

            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            $ProRequirementsList->addConditionParam("ProfessionalPath = ?", $ProProfile);

            $ProRequirementsList->setOrderKey('creationDate');
            $ProRequirementsList->setOrder('desc');

            $ProRequirements = $ProRequirementsList->load();
            $logger->info('Prorequirements Array:', ['ProRequirements' => $ProRequirements]);

            $activeRequirements = [];
            $expiredRequirements = [];

            foreach ($ProRequirements as $ProRequirement) {
                if ($ProRequirement->getExpiryCheck() === 'Active') {
                    $activeRequirements[] = $ProRequirement;
                } elseif ($ProRequirement->getExpiryCheck() === 'Expired') {
                    $expiredRequirements[] = $ProRequirement;
                }
            }

            $logger->info('activeRequirements:', ['activeRequirements' => $activeRequirements]);


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
     * @Route("/account/customers", name="account-customers")
     * @param Request $request
     * @param Security $security
     * @param Translator $translator
     * @return Response
     */
    public function DashboardCustomersAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            $subscriptionStart = $customer->getSubscriptionStart();

            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    return $this->redirect('/account/pricing');
                }
            } else {
                // First time user, show annual fee
                return $this->redirect('/account/pricing');
            }

            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            
            // Get all surveys created by this professional
            $surveysList = new \Pimcore\Model\DataObject\CustomerSurvey\Listing();
            $surveysList->addConditionParam("ProfessionalPath = ?", $ProProfile);
            $surveysList->setOrderKey('creationDate');
            $surveysList->setOrder('desc');
            $surveys = $surveysList->load();

            return $this->render('Professional/dashboard_customers.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
                'surveys' => $surveys,
            ]);
        }
        
        // If the user is not logged in or not authorized
        return $this->render('Professional/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/account/survey/create", name="survey-create")
     */
    public function createSurveyAction(Request $request, Security $security)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            return $this->render('Professional/create_survey.html.twig', [
                'ProProfile' => $ProProfile,
                'customer' => $customer,
            ]);
        }

        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/api/survey/templates", name="api-survey-templates", methods={"GET"})
     */
    public function getSurveyTemplatesAction()
    {
        return new JsonResponse([
            'with_materials' => $this->getWithMaterialsTemplate(),
            'without_materials' => $this->getWithoutMaterialsTemplate()
        ]);
    }

    /**
     * @Route("/api/survey/create", name="api-survey-create", methods={"POST"})
     */
    public function apiCreateSurveyAction(Request $request, Security $security)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $data = json_decode($request->getContent(), true);
            
            $customer = $user;
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            try {
                $survey = new \Pimcore\Model\DataObject\CustomerSurvey();
                $survey->setParent(Service::createFolderByPath('/CustomerSurveys'));
                $survey->setKey(uniqid());
                $survey->setPublished(true);
                $survey->setTitle($data['Title']);
                $survey->setFirstname($data['firstname']);
                $survey->setLastname($data['lastname']);
                $survey->setEmail($data['email']);
                $survey->setPhone($data['phone']);
                $survey->setCity($data['city']);
                
                $survey->setProfessional($ProProfile);
                
                $questionsJson = json_encode($data['sections']);
                $survey->setQuestions($questionsJson);
                
                $survey->save();
                $surveyKey = $survey->getKey();

                // Send survey email to customer
                $this->sendSurveyEmailToCustomer(
                    $data['firstname'],
                    $data['email'],
                    $surveyKey,
                    $data['Title'],
                   
                );

                return new JsonResponse(['success' => true, 'surveyId' => $survey->getId()]);
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
    }



    private function sendSurveyEmailToCustomer($firstName, $email, $surveyKey, $surveyTitle)
    {
        try {
            $surveyUrl = "https://arqonz.com/customer-survey/{$surveyKey}";
            
            $subject = "Complete Your Survey: {$surveyTitle}";
            
            $htmlContent = $this->getSurveyEmailTemplate($firstName, $surveyUrl, $surveyTitle);
            
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($email);
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
            
            
        } catch (\Exception $e) {
            
        }
    }

    private function getSurveyEmailTemplate($firstName, $surveyUrl, $surveyTitle)
    {
        return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Complete Your Survey</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background-color: #4a6fa5;
                color: white;
                padding: 20px;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
            .content {
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 0 0 5px 5px;
            }
            .button {
                display: inline-block;
                padding: 12px 24px;
                background-color: #4a6fa5;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: bold;
                margin: 20px 0;
            }
            .footer {
                margin-top: 20px;
                font-size: 12px;
                color: #777;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>{$surveyTitle}</h1>
        </div>
        <div class="content">
            <p>Dear {$firstName},</p>
            
            <p>Thank you for choosing our services! We've prepared a personalized survey to better understand your needs and preferences.</p>
            
            <p>Your input is extremely valuable to us and will help us tailor our services specifically for you.</p>
            
            <p style="text-align: center;">
                <a href="{$surveyUrl}" class="button">Complete Survey Now</a>
            </p>
            
            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p><a href="{$surveyUrl}">{$surveyUrl}</a></p>
            
            <p>This survey will take approximately 5-10 minutes to complete.</p>
            
            <p>Best regards,<br>
            The Arqonz Team</p>
        </div>
        <div class="footer">
            <p>© 2023 Arqonz. All rights reserved.</p>
            <p>If you received this email by mistake, please disregard it.</p>
        </div>
    </body>
    </html>
    HTML;
    }





    private function getWithoutMaterialsTemplate()
    {
        return [
            'sections' => [
                [
                    'title' => 'CONTACT INFO',
                    'questions' => [
                        [
                            'question' => 'First Name',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Last Name',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Email',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Phone Number',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'title' => 'YOUR ADDRESS',
                    'questions' => [
                        [
                            'question' => 'Street Address',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'City',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'State/Province',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Postal/Zip Code',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'title' => 'PROJECT ADDRESS',
                    'questions' => [
                        [
                            'question' => 'Street Address',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'City',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'State/Province',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'Postal/Zip Code',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'LEGALITIES',
                    'questions' => [
                        [
                            'question' => 'Legal or Regulatory Considerations (e.g., permits, zoning laws, heritage restrictions)',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'OPINION SCALE',
                    'questions' => [
                        [
                            'question' => 'How much do you like the space you\'re currently living in?',
                            'type' => 'single_select',
                            'options' => [
                                '1 - Not at all',
                                '2',
                                '3',
                                '4',
                                '5 - Very much'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'PROJECT TIMELINE',
                    'questions' => [
                        [
                            'question' => 'Desired Date of Start',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'Desired Date of Completion',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'Budget Range',
                    'questions' => [
                        [
                            'question' => 'Select your budget range',
                            'type' => 'single_select',
                            'options' => [
                                'Under ₹5,00,000',
                                '₹5,00,000 - ₹10,00,000',
                                '₹10,00,000 - ₹25,00,000',
                                '₹25,00,000 - ₹50,00,000',
                                '₹50,00,000 - ₹1,00,00,000',
                                'Over ₹1,00,00,000'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Project Scope',
                    'questions' => [
                        [
                            'question' => 'Please check all that apply',
                            'type' => 'multi_select',
                            'options' => [
                                'Renovation',
                                'New Construction',
                                'Single Room',
                                'Multiple Rooms',
                                'Entire Home'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Existing Plans',
                    'questions' => [
                        [
                            'question' => 'Existing Floor Plans (if available)',
                            'type' => 'multi_select',
                            'options' => [
                                'Provided (On Whatsapp)',
                                'Not available'
                            ]
                        ],
                        [
                            'question' => 'Are there any known issues that may affect the project?',
                            'type' => 'multi_select',
                            'options' => [
                                'Structural Issues',
                                'Plumbing Problems',
                                'Electrical Concerns'
                            ]
                        ],
                        [
                            'question' => 'Photos of the Existing Space (if applicable)',
                            'type' => 'multi_select',
                            'options' => [
                                'Provided (On Whatsapp)',
                                'Not available'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Living Room Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How will you primarily use your living room?',
                            'type' => 'multi_select',
                            'options' => [
                                'Family gatherings',
                                'Entertaining guests',
                                'Relaxing',
                                'Watching TV or movies',
                                'Working/Study area',
                                'Play area for kids'
                            ]
                        ],
                        [
                            'question' => 'How many people need to be seated comfortably?',
                            'type' => 'single_select',
                            'options' => [
                                '2–3 people',
                                '4–5 people',
                                '6+ people'
                            ]
                        ],
                        [
                            'question' => 'Do you have children or pets that need to be considered in the design?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, children',
                                'Yes, pets',
                                'Yes, both',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'What type of seating arrangement do you prefer?',
                            'type' => 'multi_select',
                            'options' => [
                                'Sofa and armchairs',
                                'Sectional sofa',
                                'Recliners',
                                'Modular/flexible seating',
                                'Floor cushions and casual seating'
                            ]
                        ],
                        [
                            'question' => 'Do you need extra storage in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, built-in storage (shelving, cabinetry)',
                                'Yes, hidden storage (ottomans, furniture with storage)',
                                'No, minimal storage needed'
                            ]
                        ],
                        [
                            'question' => 'How much natural light does the room get?',
                            'type' => 'single_select',
                            'options' => [
                                'Lots of natural light',
                                'Moderate natural light',
                                'Little natural light'
                            ]
                        ],
                        [
                            'question' => 'What kind of window treatments do you prefer?',
                            'type' => 'multi_select',
                            'options' => [
                                'Curtains (light, airy fabrics)',
                                'Heavy drapes (for light control and privacy)',
                                'Blinds (wood or fabric)',
                                'Shutters',
                                'No preference'
                            ]
                        ],
                        [
                            'question' => 'What type of lighting do you want in the living room?',
                            'type' => 'multi_select',
                            'options' => [
                                'Ambient lighting (general lighting)',
                                'Task lighting (reading lamps, focused lighting)',
                                'Accent lighting (to highlight artwork or features)',
                                'Dimmer switches for mood lighting',
                                'Over to you'
                            ]
                        ],
                        [
                            'question' => 'Do you need a media center/TV in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, with a large TV',
                                'Yes, with a small TV',
                                'No TV, just a sound system',
                                'No media needed'
                            ]
                        ],
                        [
                            'question' => 'Do you want any smart home features?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, smart lighting control',
                                'Yes, smart thermostat',
                                'Yes, integrated sound system',
                                'No, I prefer manual controls'
                            ]
                        ],
                        [
                            'question' => 'Do you prefer open shelving or closed storage?',
                            'type' => 'single_select',
                            'options' => [
                                'Open shelving for display',
                                'Closed cabinetry for clutter control',
                                'A mix of both'
                            ]
                        ],
                        [
                            'question' => 'Do you want a fireplace or focal point in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, a fireplace (real or electric)',
                                'Yes, a large artwork/feature wall',
                                'No specific focal point'
                            ]
                        ],
                        [
                            'question' => 'Are there any architectural features you\'d like to highlight?',
                            'type' => 'multi_select',
                            'options' => [
                                'Beams',
                                'Fireplace',
                                'Columns',
                                'None'
                            ]
                        ],
                        [
                            'question' => 'Do you have any specific decor preferences?',
                            'type' => 'multi_select',
                            'options' => [
                                'Artwork (paintings, prints)',
                                'Mirrors',
                                'Sculptures',
                                'Plants and greenery',
                                'No strong preferences'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Kitchen Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How much storage do you need in your kitchen?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (basic cabinets)',
                                'Moderate (standard cabinetry and pantry)',
                                'Extensive (extra cabinetry, pantry, and island storage)'
                            ]
                        ],
                        [
                            'question' => 'Do you need any specialty appliances or features?',
                            'type' => 'multi_select',
                            'options' => [
                                'Double oven',
                                'Wine fridge',
                                'Large refrigerator/freezer',
                                'Built-in coffee station',
                                'No special appliances'
                            ]
                        ],
                        [
                            'question' => 'Do you want an island or breakfast bar in the kitchen?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, an island',
                                'Yes, a breakfast bar',
                                'No, neither'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Dining Room Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is your preferred dining table shape?',
                            'type' => 'single_select',
                            'options' => [
                                'Rectangular',
                                'Round',
                                'Oval',
                                'Square'
                            ]
                        ],
                        [
                            'question' => 'How many people do you want to seat comfortably in the dining room?',
                            'type' => 'single_select',
                            'options' => [
                                '4 people',
                                '6 people',
                                '8+ people'
                            ]
                        ],
                        [
                            'question' => 'Do you need space for additional storage or serving areas (buffet, sideboard)?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Would you like to incorporate a casual dining area (breakfast nook)?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Bedroom Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is the primary function of the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Sleeping and relaxing only',
                                'Sleeping, relaxing, and working',
                                'Multi-functional (sleeping, working, exercise area)'
                            ]
                        ],
                        [
                            'question' => 'What kind of bed do you prefer?',
                            'type' => 'single_select',
                            'options' => [
                                'Standard bed',
                                'Canopy bed',
                                'Platform bed',
                                'Storage bed'
                            ]
                        ],
                        [
                            'question' => 'Do you need a walk-in-wardrobe?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you need a designated workspace in the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you prefer a seating area in the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Bathroom Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'Do you prefer a shower, bathtub, or both?',
                            'type' => 'single_select',
                            'options' => [
                                'Shower only',
                                'Bathtub only',
                                'Both'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in the bathroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (vanity storage only)',
                                'Moderate (vanity and wall storage)',
                                'Extensive (extra shelving, linen closet)'
                            ]
                        ],
                        [
                            'question' => 'How many people will use the bathroom regularly?',
                            'type' => 'single_select',
                            'options' => [
                                '1 person',
                                '2 people',
                                'More than 2 people'
                            ]
                        ],
                        [
                            'question' => 'Do you require a double vanity or single vanity?',
                            'type' => 'single_select',
                            'options' => [
                                'Single vanity',
                                'Double vanity'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Outdoor Spaces Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How do you plan to use your outdoor space?',
                            'type' => 'multi_select',
                            'options' => [
                                'Relaxing and lounging',
                                'Entertaining guests',
                                'Dining and BBQ',
                                'Gardening'
                            ]
                        ],
                        [
                            'question' => 'Do you need a covered or shaded area?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, covered area',
                                'No, open space preferred'
                            ]
                        ],
                        [
                            'question' => 'Do you require any specific features in the outdoor space?',
                            'type' => 'multi_select',
                            'options' => [
                                'Outdoor kitchen',
                                'Fire pit',
                                'Pool or hot tub',
                                'Outdoor seating only'
                            ]
                        ],
                        [
                            'question' => 'How many people do you plan to accommodate outdoors?',
                            'type' => 'single_select',
                            'options' => [
                                '2–4 people',
                                '4–6 people',
                                'More than 6 people'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Home Office Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is the primary function of your home office?',
                            'type' => 'single_select',
                            'options' => [
                                'Work only',
                                'Study and work',
                                'Multi-purpose (work, hobbies, meetings)'
                            ]
                        ],
                        [
                            'question' => 'How many workstations do you need?',
                            'type' => 'single_select',
                            'options' => [
                                '1 workstation',
                                '2 workstations',
                                'More than 2 workstations'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in the office?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (desk storage)',
                                'Moderate (bookshelves, filing cabinets)',
                                'Extensive (multiple storage options)'
                            ]
                        ],
                        [
                            'question' => 'Do you need seating for guests or clients?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you want to incorporate a meeting space in the office?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Additional Rooms and Other Areas',
                    'questions' => [
                        [
                            'question' => 'Do you have any additional rooms that need designing?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, a playroom',
                                'Yes, a home gym',
                                'Yes, a laundry room',
                                'Yes, a guest bedroom',
                                'No additional rooms'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in additional rooms (if any)?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal',
                                'Moderate',
                                'Extensive'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Smart Home and Project Details',
                    'questions' => [
                        [
                            'question' => 'Do you want to incorporate smart home features in your project?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, smart lighting control',
                                'Yes, smart thermostat',
                                'Yes, smart security system',
                                'No smart features needed'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Inspirational Sources',
                    'questions' => [
                        [
                            'question' => 'Please share any sources of inspiration for your design project (provide links)',
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        ];
    }



    private function getWithMaterialsTemplate()
    {
        return [
            'sections' => [
                [
                    'title' => 'CONTACT INFO',
                    'questions' => [
                        [
                            'question' => 'First Name',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Last Name',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Email',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Phone Number',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'title' => 'YOUR ADDRESS',
                    'questions' => [
                        [
                            'question' => 'Street Address',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'City',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'State/Province',
                            'type' => 'text',
                            'required' => true
                        ],
                        [
                            'question' => 'Postal/Zip Code',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'title' => 'PROJECT ADDRESS',
                    'questions' => [
                        [
                            'question' => 'Street Address',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'City',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'State/Province',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'Postal/Zip Code',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'LEGALITIES',
                    'questions' => [
                        [
                            'question' => 'Legal or Regulatory Considerations (e.g., permits, zoning laws, heritage restrictions)',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'OPINION SCALE',
                    'questions' => [
                        [
                            'question' => 'How much do you like the space you\'re currently living in?',
                            'type' => 'single_select',
                            'options' => [
                                '1 - Not at all',
                                '2',
                                '3',
                                '4',
                                '5 - Very much'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'PROJECT TIMELINE',
                    'questions' => [
                        [
                            'question' => 'Desired Date of Start',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'Desired Date of Completion',
                            'type' => 'text'
                        ]
                    ]
                ],
                [
                    'title' => 'Budget Range',
                    'questions' => [
                        [
                            'question' => 'Select your budget range',
                            'type' => 'single_select',
                            'options' => [
                                'Under ₹5,00,000',
                                '₹5,00,000 - ₹10,00,000',
                                '₹10,00,000 - ₹25,00,000',
                                '₹25,00,000 - ₹50,00,000',
                                '₹50,00,000 - ₹1,00,00,000',
                                'Over ₹1,00,00,000'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Project Scope',
                    'questions' => [
                        [
                            'question' => 'Please check all that apply',
                            'type' => 'multi_select',
                            'options' => [
                                'Renovation',
                                'New Construction',
                                'Single Room',
                                'Multiple Rooms',
                                'Entire Home'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Existing Plans',
                    'questions' => [
                        [
                            'question' => 'Existing Floor Plans (if available)',
                            'type' => 'multi_select',
                            'options' => [
                                'Provided (On Whatsapp)',
                                'Not available'
                            ]
                        ],
                        [
                            'question' => 'Are there any known issues that may affect the project?',
                            'type' => 'multi_select',
                            'options' => [
                                'Structural Issues',
                                'Plumbing Problems',
                                'Electrical Concerns'
                            ]
                        ],
                        [
                            'question' => 'Photos of the Existing Space (if applicable)',
                            'type' => 'multi_select',
                            'options' => [
                                'Provided (On Whatsapp)',
                                'Not available'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Living Room Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How will you primarily use your living room?',
                            'type' => 'multi_select',
                            'options' => [
                                'Family gatherings',
                                'Entertaining guests',
                                'Relaxing',
                                'Watching TV or movies',
                                'Working/Study area',
                                'Play area for kids'
                            ],
                            'required' => true
                        ],
                        [
                            'question' => 'How many people need to be seated comfortably?',
                            'type' => 'single_select',
                            'options' => [
                                '2–3 people',
                                '4–5 people',
                                '6+ people'
                            ]
                        ],
                        [
                            'question' => 'Do you have children or pets that need to be considered in the design?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, children',
                                'Yes, pets',
                                'Yes, both',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'What type of seating arrangement do you prefer?',
                            'type' => 'multi_select',
                            'options' => [
                                'Sofa and armchairs',
                                'Sectional sofa',
                                'Recliners',
                                'Modular/flexible seating',
                                'Floor cushions and casual seating'
                            ]
                        ],
                        [
                            'question' => 'Do you need extra storage in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, built-in storage (shelving, cabinetry)',
                                'Yes, hidden storage (ottomans, furniture with storage)',
                                'No, minimal storage needed'
                            ]
                        ],
                        [
                            'question' => 'How much natural light does the room get?',
                            'type' => 'single_select',
                            'options' => [
                                'Lots of natural light',
                                'Moderate natural light',
                                'Little natural light'
                            ]
                        ],
                        [
                            'question' => 'What kind of window treatments do you prefer?',
                            'type' => 'multi_select',
                            'options' => [
                                'Curtains (light, airy fabrics)',
                                'Heavy drapes (for light control and privacy)',
                                'Blinds (wood or fabric)',
                                'Shutters',
                                'No preference'
                            ]
                        ],
                        [
                            'question' => 'What type of lighting do you want in the living room?',
                            'type' => 'multi_select',
                            'options' => [
                                'Ambient lighting (general lighting)',
                                'Task lighting (reading lamps, focused lighting)',
                                'Accent lighting (to highlight artwork or features)',
                                'Dimmer switches for mood lighting',
                                'Over to you'
                            ]
                        ],
                        [
                            'question' => 'Do you need a media center/TV in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, with a large TV',
                                'Yes, with a small TV',
                                'No TV, just a sound system',
                                'No media needed'
                            ]
                        ],
                        [
                            'question' => 'Do you want any smart home features?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, smart lighting control',
                                'Yes, smart thermostat',
                                'Yes, integrated sound system',
                                'No, I prefer manual controls'
                            ]
                        ],
                        [
                            'question' => 'Do you prefer open shelving or closed storage?',
                            'type' => 'single_select',
                            'options' => [
                                'Open shelving for display',
                                'Closed cabinetry for clutter control',
                                'A mix of both'
                            ]
                        ],
                        [
                            'question' => 'Do you want a fireplace or focal point in the living room?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, a fireplace (real or electric)',
                                'Yes, a large artwork/feature wall',
                                'No specific focal point'
                            ]
                        ],
                        [
                            'question' => 'Are there any architectural features you\'d like to highlight?',
                            'type' => 'multi_select',
                            'options' => [
                                'Beams',
                                'Fireplace',
                                'Columns',
                                'None'
                            ]
                        ],
                        [
                            'question' => 'Do you have any specific decor preferences?',
                            'type' => 'multi_select',
                            'options' => [
                                'Artwork (paintings, prints)',
                                'Mirrors',
                                'Sculptures',
                                'Plants and greenery',
                                'No strong preferences'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Hardwood',
                                'Laminate',
                                'Tile',
                                'Carpet',
                                'Concrete',
                                'Natural stone'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Walls',
                            'type' => 'multi_select',
                            'options' => [
                                'Paint',
                                'Wallpaper',
                                'Wood paneling',
                                'Stone',
                                'Brick',
                                'Concrete'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Furniture',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Glass',
                                'Upholstered',
                                'Leather',
                                'Sustainable/Eco-friendly materials'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Kitchen Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How much storage do you need in your kitchen?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (basic cabinets)',
                                'Moderate (standard cabinetry and pantry)',
                                'Extensive (extra cabinetry, pantry, and island storage)'
                            ]
                        ],
                        [
                            'question' => 'Do you need any specialty appliances or features?',
                            'type' => 'multi_select',
                            'options' => [
                                'Double oven',
                                'Wine fridge',
                                'Large refrigerator/freezer',
                                'Built-in coffee station',
                                'No special appliances'
                            ]
                        ],
                        [
                            'question' => 'Do you want an island or breakfast bar in the kitchen?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, an island',
                                'Yes, a breakfast bar',
                                'No, neither'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Countertops',
                            'type' => 'multi_select',
                            'options' => [
                                'Granite',
                                'Marble',
                                'Quartz',
                                'Concrete',
                                'Butcher block/Wood',
                                'Laminate'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Cabinetry',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood (natural finish)',
                                'Wood (painted)',
                                'Laminate',
                                'Metal',
                                'Glass-fronted'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Backsplash',
                            'type' => 'multi_select',
                            'options' => [
                                'Ceramic tile',
                                'Glass tile',
                                'Natural stone',
                                'Same as countertop',
                                'Metal'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Tile',
                                'Hardwood',
                                'Vinyl',
                                'Concrete',
                                'Natural stone'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Dining Room Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is your preferred dining table shape?',
                            'type' => 'single_select',
                            'options' => [
                                'Rectangular',
                                'Round',
                                'Oval',
                                'Square'
                            ]
                        ],
                        [
                            'question' => 'How many people do you want to seat comfortably in the dining room?',
                            'type' => 'single_select',
                            'options' => [
                                '4 people',
                                '6 people',
                                '8+ people'
                            ]
                        ],
                        [
                            'question' => 'Do you need space for additional storage or serving areas (buffet, sideboard)?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Would you like to incorporate a casual dining area (breakfast nook)?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Dining Table',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Glass',
                                'Metal',
                                'Marble/Stone',
                                'Mixed materials'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Chairs',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Upholstered',
                                'Plastic/Acrylic',
                                'Mixed materials'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Hardwood',
                                'Tile',
                                'Carpet',
                                'Natural stone',
                                'Laminate'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Bedroom Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is the primary function of the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Sleeping and relaxing only',
                                'Sleeping, relaxing, and working',
                                'Multi-functional (sleeping, working, exercise area)'
                            ]
                        ],
                        [
                            'question' => 'What kind of bed do you prefer?',
                            'type' => 'single_select',
                            'options' => [
                                'Standard bed',
                                'Canopy bed',
                                'Platform bed',
                                'Storage bed'
                            ]
                        ],
                        [
                            'question' => 'Do you need a walk-in-wardrobe?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you need a designated workspace in the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you prefer a seating area in the bedroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Bedroom Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Carpet',
                                'Hardwood',
                                'Laminate',
                                'Tile',
                                'Area rugs over hard flooring'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Bed Frame',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Upholstered',
                                'Leather',
                                'Mixed materials'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Bedroom Furniture',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Glass',
                                'Mirrored surfaces',
                                'Upholstered pieces'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Bathroom Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'Do you prefer a shower, bathtub, or both?',
                            'type' => 'single_select',
                            'options' => [
                                'Shower only',
                                'Bathtub only',
                                'Both'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in the bathroom?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (vanity storage only)',
                                'Moderate (vanity and wall storage)',
                                'Extensive (extra shelving, linen closet)'
                            ]
                        ],
                        [
                            'question' => 'How many people will use the bathroom regularly?',
                            'type' => 'single_select',
                            'options' => [
                                '1 person',
                                '2 people',
                                'More than 2 people'
                            ]
                        ],
                        [
                            'question' => 'Do you require a double vanity or single vanity?',
                            'type' => 'single_select',
                            'options' => [
                                'Single vanity',
                                'Double vanity'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Bathroom Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Ceramic tile',
                                'Porcelain tile',
                                'Natural stone',
                                'Vinyl',
                                'Concrete'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Shower/Tub Surround',
                            'type' => 'multi_select',
                            'options' => [
                                'Ceramic tile',
                                'Porcelain tile',
                                'Natural stone',
                                'Glass',
                                'Solid surface (e.g., Corian)'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Vanity',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Laminate',
                                'Metal',
                                'Glass',
                                'Stone/composite'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Countertops',
                            'type' => 'multi_select',
                            'options' => [
                                'Granite',
                                'Marble',
                                'Quartz',
                                'Concrete',
                                'Solid surface (e.g., Corian)'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Outdoor Spaces Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'How do you plan to use your outdoor space?',
                            'type' => 'multi_select',
                            'options' => [
                                'Relaxing and lounging',
                                'Entertaining guests',
                                'Dining and BBQ',
                                'Gardening'
                            ]
                        ],
                        [
                            'question' => 'Do you need a covered or shaded area?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes, covered area',
                                'No, open space preferred'
                            ]
                        ],
                        [
                            'question' => 'Do you require any specific features in the outdoor space?',
                            'type' => 'multi_select',
                            'options' => [
                                'Outdoor kitchen',
                                'Fire pit',
                                'Pool or hot tub',
                                'Outdoor seating only'
                            ]
                        ],
                        [
                            'question' => 'How many people do you plan to accommodate outdoors?',
                            'type' => 'single_select',
                            'options' => [
                                '2–4 people',
                                '4–6 people',
                                'More than 6 people'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Outdoor Flooring/Decking',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Composite decking',
                                'Stone/pavers',
                                'Concrete',
                                'Brick'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Outdoor Furniture',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Wicker/Rattan',
                                'Plastic/Resin',
                                'Concrete'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Shade Structures',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood pergola',
                                'Metal gazebo',
                                'Canvas awning',
                                'Fabric sails',
                                'Natural (trees/plants)'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Home Office Design Questionnaire',
                    'questions' => [
                        [
                            'question' => 'What is the primary function of your home office?',
                            'type' => 'single_select',
                            'options' => [
                                'Work only',
                                'Study and work',
                                'Multi-purpose (work, hobbies, meetings)'
                            ]
                        ],
                        [
                            'question' => 'How many workstations do you need?',
                            'type' => 'single_select',
                            'options' => [
                                '1 workstation',
                                '2 workstations',
                                'More than 2 workstations'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in the office?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal (desk storage)',
                                'Moderate (bookshelves, filing cabinets)',
                                'Extensive (multiple storage options)'
                            ]
                        ],
                        [
                            'question' => 'Do you need seating for guests or clients?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Do you want to incorporate a meeting space in the office?',
                            'type' => 'single_select',
                            'options' => [
                                'Yes',
                                'No'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Desk',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Glass',
                                'Laminate',
                                'Mixed materials'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Office Chair',
                            'type' => 'multi_select',
                            'options' => [
                                'Leather',
                                'Fabric upholstery',
                                'Mesh',
                                'Plastic/Modern materials',
                                'Wood'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Storage/Shelving',
                            'type' => 'multi_select',
                            'options' => [
                                'Wood',
                                'Metal',
                                'Glass',
                                'Laminate',
                                'Mixed materials'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Additional Rooms and Other Areas',
                    'questions' => [
                        [
                            'question' => 'Do you have any additional rooms that need designing?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, a playroom',
                                'Yes, a home gym',
                                'Yes, a laundry room',
                                'Yes, a guest bedroom',
                                'No additional rooms'
                            ]
                        ],
                        [
                            'question' => 'What kind of storage do you need in additional rooms (if any)?',
                            'type' => 'single_select',
                            'options' => [
                                'Minimal',
                                'Moderate',
                                'Extensive'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Additional Room Flooring',
                            'type' => 'multi_select',
                            'options' => [
                                'Carpet',
                                'Hardwood',
                                'Laminate',
                                'Vinyl',
                                'Rubber (for gym)',
                                'Tile'
                            ]
                        ],
                        [
                            'question' => 'Materials Preference for Additional Room Walls',
                            'type' => 'multi_select',
                            'options' => [
                                'Paint',
                                'Wallpaper',
                                'Wood paneling',
                                'Acoustic panels',
                                'Mirrors (for gym)'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Smart Home and Project Details',
                    'questions' => [
                        [
                            'question' => 'Do you want to incorporate smart home features in your project?',
                            'type' => 'multi_select',
                            'options' => [
                                'Yes, smart lighting control',
                                'Yes, smart thermostat',
                                'Yes, smart security system',
                                'No smart features needed'
                            ]
                        ],
                        [
                            'question' => 'Material Quality Preference',
                            'type' => 'single_select',
                            'options' => [
                                'Budget-friendly materials',
                                'Mid-range quality materials',
                                'High-end luxury materials',
                                'Mix of quality tiers based on importance'
                            ]
                        ],
                        [
                            'question' => 'Material Sustainability Preference',
                            'type' => 'multi_select',
                            'options' => [
                                'Eco-friendly/sustainable materials',
                                'Recycled/reclaimed materials',
                                'Local sourcing when possible',
                                'Low VOC/non-toxic materials',
                                'No specific sustainability requirements'
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Inspirational Sources',
                    'questions' => [
                        [
                            'question' => 'Please share any sources of inspiration for your design project (provide links)',
                            'type' => 'text'
                        ],
                        [
                            'question' => 'Materials and Finishes Inspiration',
                            'type' => 'text',
                            'placeholder' => 'Describe any specific materials, colors, or finishes you\'ve seen and like'
                        ]
                    ]
                ],
                // ... other room questionnaires
            ]
        ];
    }




    /**
     * @Route("/customer-survey/{surveyId}", name="customer_survey")
     */
    public function showSurveyAction(Request $request, $surveyId)
    {
        $survey = CustomerSurvey::getByPath("/CustomerSurveys/$surveyId");
        
        if (!$survey || !$survey->isPublished()) {
            throw $this->createNotFoundException('Survey not found');
        }

        $sections = json_decode($survey->getQuestions(), true);
        
        return $this->render('Professional/survey_form.html.twig', [
            'survey' => $survey,
            'sections' => $sections,
            'surveyId' => $surveyId
        ]);
    }

    /**
     * @Route("/api/survey/{surveyId}/submit", name="api_survey_submit", methods={"POST"})
     */
    public function submitSurveyAction(Request $request, $surveyId)
    {
        $survey = CustomerSurvey::getByPath("/CustomerSurveys/$surveyId");
        
        if (!$survey || !$survey->isPublished()) {
            return $this->json(['success' => false, 'message' => 'Survey not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $answers = $data['answers'] ?? [];
        
        try {
            $surveyAnswer = new CustomerSurveyAnswer();
            $surveyAnswer->setParent(Service::createFolderByPath('/CustomerSurveyAnswers'));
            $surveyAnswer->setKey(uniqid());
            $surveyAnswer->setPublished(true);
            $surveyAnswer->setSurvey($survey);
            $surveyAnswer->setAnswers(json_encode($answers));
            $surveyAnswer->save();

            return $this->json(['success' => true, 'message' => 'Survey submitted successfully']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    
    /**
     * @Route("/account/survey/responses/{surveyId}", name="survey_responses")
     */
    public function viewSurveyResponsesAction(Request $request, Security $security, $surveyId)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            $survey = CustomerSurvey::getByPath("/CustomerSurveys/$surveyId");
            
            if (!$survey || !$survey->isPublished()) {
                throw $this->createNotFoundException('Survey not found');
            }

            // Get all answers for this survey using reverse relation
            $answers = $survey->getCustomerSurveyAnswer();
            
            if (empty($answers)) {
                return $this->render('Professional/survey_no_responses.html.twig', [
                    'survey' => $survey
                ]);
            }

            // Get the original survey questions structure
            $surveyQuestions = json_decode($survey->getQuestions(), true);
            
            // Process all answers
            $processedAnswers = [];
            foreach ($answers as $answer) {
                $answerData = json_decode($answer->getAnswers(), true);
                $processedData = [];
                
                // Process the answer data to match the survey structure
                foreach ($surveyQuestions as $section) {
                    $sectionTitle = $section['title'];
                    $processedData[$sectionTitle] = [];
                    
                    foreach ($section['questions'] as $question) {
                        $questionText = $question['question'];
                        
                        // Create the exact key format as stored in the database
                        $key = "answers[$sectionTitle][$questionText]";
                        $value = null;
                        
                        // Find the answer in the JSON data - handle encoding issues
                        if (isset($answerData[$key])) {
                            $value = $answerData[$key];
                        } else {
                            // Try with unicode escaped format
                            $encodedSectionTitle = str_replace(' ', '\\u0020', $sectionTitle);
                            $encodedQuestionText = str_replace(' ', '\\u0020', $questionText);
                            $encodedKey = "answers[$encodedSectionTitle][$encodedQuestionText]";
                            
                            if (isset($answerData[$encodedKey])) {
                                $value = $answerData[$encodedKey];
                            } else {
                                // Try even more encoding variations if needed
                                $encodedKey = "answers[" . str_replace([' ', '?'], ['\\u0020', '\\u003F'], $sectionTitle) . "][" . 
                                    str_replace([' ', '?'], ['\\u0020', '\\u003F'], $questionText) . "]";
                                
                                if (isset($answerData[$encodedKey])) {
                                    $value = $answerData[$encodedKey];
                                }
                            }
                        }
                        
                        // Decode Unicode escape sequences in values
                        if ($value !== null) {
                            if (is_array($value)) {
                                // Handle arrays (multi-select answers)
                                $decodedArray = [];
                                foreach ($value as $item) {
                                    if (is_string($item)) {
                                        // Decode Unicode escape sequences in the string
                                        $decodedArray[] = $this->decodeUnicodeEscapes($item);
                                    } else {
                                        $decodedArray[] = $item;
                                    }
                                }
                                $value = $decodedArray;
                            } elseif (is_string($value)) {
                                // Decode Unicode escape sequences in the string
                                $value = $this->decodeUnicodeEscapes($value);
                            }
                        }
                        
                        $processedData[$sectionTitle][$questionText] = $value;
                    }
                }
                
                $processedAnswers[] = [
                    'object' => $answer,
                    'data' => $processedData,
                    'creationDate' => $answer->getCreationDate()
                ];
            }

            return $this->render('Professional/survey_responses.html.twig', [
                'survey' => $survey,
                'answers' => $processedAnswers,
                'surveyQuestions' => $surveyQuestions,
                'customer' => $customer,
            ]);
        }
        
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * Helper method to decode Unicode escape sequences in strings
     */
    private function decodeUnicodeEscapes($str) 
    {
        // Replace standard format \\u0020 with actual space
        $str = str_replace('\\u0020', ' ', $str);
        
        // Replace question mark encoding
        $str = str_replace('\\u003F', '?', $str);
        
        // Handle other common encodings
        $str = str_replace('\\u0022', '"', $str);
        $str = str_replace('\\u0027', "'", $str);
        $str = str_replace('\\u002C', ',', $str);
        $str = str_replace('\\u002E', '.', $str);
        
        // For other Unicode characters that may be escaped
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function ($matches) {
                return html_entity_decode('&#x' . $matches[1] . ';', ENT_QUOTES, 'UTF-8');
            },
            $str
        );
    }


    /**
     * 
     *
     * @Route("/account/Vehicles", name="account-Vehicles")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardVehiclesAction(Request $request, Security $security, Translator $translator, LoggerInterface $logger)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $ProContractorVehicles = $ProProfile->getVehicles();



            return $this->render('/Professional/Dashboard/dashboard_vehicles.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'ProContractorVehicles' => $ProContractorVehicles,
                
                ]);
            
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * 
     *
     * @Route("/account/Add-Vehicle", name="account-Add-Vehicle")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function DashboardAddVehiclesAction(Request $request, Security $security, Translator $translator, LoggerInterface $logger)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];

            $form = $this->createForm(AddVehicleFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProContractorVehicle = new ProContractorVehicle();
                $ProContractorVehicle->setParent(Service::createFolderByPath('/Services/Contractors/Vehicles'));
                $ProContractorVehicle->setKey(Text::toUrl($formData['VehicleNumber'] . '-' . time()));
                $ProContractorVehicle->setProfessional($ProProfile);
                $ProContractorVehicle->setVehicleBrand($form->get('VehicleBrand')->getData());
                $ProContractorVehicle->setVehicleType($form->get('VehicleType')->getData());
                $ProContractorVehicle->setVehicleModel($form->get('VehicleModel')->getData());
                $ProContractorVehicle->setVehicleNumber($form->get('VehicleNumber')->getData());
                $ProContractorVehicle->setYearManufactured($form->get('YearManufactured')->getData());
                $ProContractorVehicle->setEngineType($form->get('EngineType')->getData());
                $ProContractorVehicle->setCapacity($form->get('Capacity')->getData());
                $ProContractorVehicle->setAvailabilityStatus($form->get('AvailabilityStatus')->getData());
                $ProContractorVehicle->setInsuranceStatus($form->get('InsuranceStatus')->getData());
                $ProContractorVehicle->setOperatorProvided($form->get('OperatorProvided')->getData());
                $ProContractorVehicle->setPricePerHour($form->get('PriceForHour')->getData());
                $ProContractorVehicle->setUsageRestrictions($form->get('UsageRestrictions')->getData());
                $ProContractorVehicle->setCitiesServed(implode(',', $form->get('CitiesServed')->getData()));

                $galleryData = $form->get('VehicleGallery')->getData();
                    $items = [];
                    $hotspotImages = [];

                    foreach ($galleryData as $file) {
                        $hotspotImage = new Hotspotimage();
                        // Check if the file is an instance of UploadedFile
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Create a new Image instance and set it for Hotspotimage
                            $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/Vehicles"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                }
                $ProContractorVehicle->setVehicleGallery(new ImageGallery($items));  
                $ProContractorVehicle->setPublished(true);
                $ProContractorVehicle->save();

                $this->addFlash('success', $translator->trans('Vehicle submitted succesfully.'));
                return $this->redirectToRoute('account-Vehicles');    
            }
            

            



            return $this->render('/Professional/Dashboard/dashboard_add_Vehicles.html.twig', [
                    'ProProfile' => $ProProfile,
                    'customer' => $customer,
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    // 'ProContractorVehicle' => $ProContractorVehicle,
                
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
                        'Dealer' => '/Services/Dealers/Requirements',
                        'Distributor' => '/Services/Distributors/Requirements',
                        'Manufacturer' => '/Services/Manufacturers/Requirements',
                        'Engineer' => '/Services/Engineers/Requirements',
                        'Professional' => '/Services/Professionals/Requirements',
                        'Supplier' => '/Services/Suppliers/Requirements',
                        
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
                            $proRequirementProduct->setProdType($rowData[4]);
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

                    $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                    $EmailTemplates->addConditionParam("TemplateName = ?", "BOQUploadSuccess");
                    $EmailTemplate = $EmailTemplates->load();
                    $EmailTemplate = $EmailTemplate[0];

                    $subject = $EmailTemplate->getSubject();
                    $htmlContent = $EmailTemplate->getContent();
                    $htmlContent = str_replace("[Professional Name]", $ProProfile->getCompanyName(), $htmlContent);
                    // Create a new Pimcore\Mail instance
                    $mail = new \Pimcore\Mail();
                    // $mail->from('arqonztest@gmail.com');
                    $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                    $mail->to($customer->getEmail());
                    $mail->subject($subject);
                    $mail->html($htmlContent);
                    $mail->send();

                    $this->addFlash('success', $translator->trans('Requirements submitted successfully.'));
                    return $this->redirectToRoute('account-Requirements');


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
            $subscriptionStart = $customer->getSubscriptionStart();

            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    return $this->redirect('/account/pricing');
                }
            } else {
                // First time user, show annual fee
                return $this->redirect('/account/pricing');
            }

            // $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $customertype = $ProProfile->getPortfolioType();

            // $ProProfilePath = "/Services/" . $customertype . "s/Profiles/" . $ProProfile->getKey();

            // $ProEnquiryList = new \Pimcore\Model\DataObject\ProEnquiry\Listing();
            // $ProEnquiryList->addConditionParam("ProfessionalPath = ?", $ProProfilePath);

            // $ProEnquiryList->setOrderKey('creationDate');
            // $ProEnquiryList->setOrder('desc');

            $ProEnquiries = $ProProfile->getEnquiries();
            $ProEnquiries = array_reverse($ProEnquiries);


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
            $subscriptionStart = $customer->getSubscriptionStart();

            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    return $this->redirect('/account/pricing');
                }
            } else {
                // First time user, show annual fee
                return $this->redirect('/account/pricing');
            }

            $customertype = $customer->getcustomertype();
            $customerID = $customer->getUserID();
            //$ProProfile = $customer->getPortfolio();
            $form = $this->createForm(EndorsementRequestFormType::class);
            $form->handleRequest($request);
            $EndorsementFormURL = '/user/' . $customerID . '/endorsement/';
            $Endorsements = $customer->getEndorsement();
            $numberOfEndorsements = count($Endorsements);



            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProEndorsementRequest= new ProEndorsementRequest();
                $ProEndorsementRequest->setParent(Service::createFolderByPath('/Endorsement/Endorsement'));
                $ProEndorsementRequest->setKey(Text::toUrl(time()));
                $ProEndorsementRequest->setName($form->get('Name')->getData());
                $ProEndorsementRequest->setEmail($form->get('Email')->getData());
                $ProEndorsementRequest->setPhone($form->get('Phone')->getData());
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
                $endorsementurl = "<a href='https://arqonz.com/user/{$customer->getUserID()}/endorsement' style='text-align:center;'>Click Here to Endorse Now</a>";
                
                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                $subject = str_replace("[customer]", $customer->getfirstname(), $subject);
                
                $htmlContent = str_replace("[Refferer]", $form->get('Name')->getData(), $htmlContent);
                $htmlContent = str_replace("[customer]", $customer->getfirstname(), $htmlContent);
                $htmlContent = str_replace("[EndorsementURL]", $endorsementurl, $htmlContent);
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
                return $this->redirectToRoute('account-index');

            }


            return $this->render('Professional/dashboard_Endorsement.html.twig', [
                    'customer' => $customer,
                    'form' => $form->createView(),
                    'Endorsements' => $Endorsements,
                    'numberOfEndorsements' => $numberOfEndorsements,
                
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
            if ($customertype == 'Dealer') {
                $form = $this->createForm(DealerRegistrationFormType::class);
            }

            if ($customertype == 'Professional') {
                $form = $this->createForm(ProfessionalRegistrationFormType::class);
            }
            if ($customertype == 'Supplier') {
                $form = $this->createForm(SupplierRegistrationFormType::class);
            }

            $form->handleRequest($request);
            $PortfolioActivate = $customer->getPortfolioActivate();

            // $portfolioType = $form->get('Profession')->getData();

            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                $ProProfile = new ProProfile();
                $portfolioType = $form->get('Profession')->getData();
                if ($portfolioType == 'Contractor') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Contractors/Profiles'));
                }
                if ($portfolioType == 'Designer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Designers/Profiles'));
                }
                if ($portfolioType == 'Architect') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Architects/Profiles'));
                }
                if ($portfolioType == 'Builder') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Builders/Profiles'));
                }
                if ($portfolioType == 'Manufacturer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Manufacturers/Profiles'));
                }
                if ($portfolioType == 'Engineer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Engineers/Profiles'));
                }
                if ($portfolioType == 'Distributor') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Distributors/Profiles'));
                }
                if ($portfolioType == 'Retailer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Retailers/Profiles'));
                }
                if ($portfolioType == 'Dealer') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Dealers/Profiles'));
                }
                if ($portfolioType == 'Professional') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Professionals/Profiles'));
                }
                if ($portfolioType == 'Supplier') {
                    $ProProfile->setParent(Service::createFolderByPath('/Services/Suppliers/Profiles'));
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
                    if ($customertype == 'Dealer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProfileGallery"));
                    }

                    if ($customertype == 'Professional') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Professionals/ProfileGallery"));
                    }
                    if ($customertype == 'Supplier') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProfileGallery"));
                    }
                    $newAsset->setData(file_get_contents($imageData->getPathname()));
                    $newAsset->save();
                    $ProProfile->setProfileImage($newAsset);
                }
            
                $ProProfile->setKey(Text::toUrl($formData['CompanyName'] . '-' . time()));
                $ProProfile->setCompanyName($form->get('CompanyName')->getData());
                if ($customertype != 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer' && $customertype !== 'Dealer' && $customertype !== 'Supplier') {

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
                if ($customertype != 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer' && $customertype !== 'Supplier') {
                    if($form->has('YearOfCertification') && $form->get('YearOfCertification')->getData()){
                        $ProProfile->setYearOfCertification($form->get('YearOfCertification')->getData());
                    }
                    if ($customertype == 'Architect') {
                        if($form->has('CoaNumber') && $form->get('CoaNumber')->getData()){
                            $ProProfile->setCoaNumber($form->get('CoaNumber')->getData());
                        }
                    }
                }

                $ProProfile->setProfessionalSupplierType($form->get('Profession')->getData());
                
                if($form->get('CitiesServed')->getData()){
                    $ProProfile->setCitiesServed(implode(',', $form->get('CitiesServed')->getData()));
                }
                $ProProfile->setDescription($form->get('Description')->getData());

                if ($customertype !== 'Builder' && $customertype !== 'Manufacturer' && $customertype !== 'Distributor' && $customertype !== 'Retailer' && $customertype !== 'Dealer' && $customertype !== 'Supplier') {
                    if($form->get('PriceForHour')->getData()){
                        $ProProfile->setPriceForHour($form->get('PriceForHour')->getData());
                    }
                }

                if ($customertype === 'Contractor') {
                    if($form->get('ContractorType')->getData()){
                        $ProProfile->setContractorType($form->get('ContractorType')->getData());
                    }
                }

                

                $ProProfile->setCountryCode($form->get('CountryCode')->getData());
                $ProProfile->setPhoneNumber($form->get('PhoneNumber')->getData());
                $ProProfile->setStreetAddress($form->get('StreetAddress')->getData());
                $ProProfile->setCity($form->get('City')->getData());
                $ProProfile->setState($form->get('State')->getData());
                $ProProfile->setCountry($form->get('Country')->getData());
                $ProProfile->setPinCode($form->get('PinCode')->getData());
                $ProProfile->setPortfolioType($form->get('Profession')->getData());
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

                // $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                // $EmailTemplates->addConditionParam("TemplateName = ?", "CompanyProfileSuccess");
                // $EmailTemplate = $EmailTemplates->load();
                // $EmailTemplate = $EmailTemplate[0];

                // $subject = $EmailTemplate->getSubject();
                // $htmlContent = $EmailTemplate->getContent();
                // eval("\$htmlContent = \"$htmlContent\";");
                // // Create a new Pimcore\Mail instance
                // $mail = new \Pimcore\Mail();
                // // $mail->from('arqonztest@gmail.com');
                // $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                // $mail->to($customer->getEmail());
                // $mail->subject($subject);
                // $mail->html($htmlContent);
                // $mail->send();

                $this->addFlash('success', $translator->trans('Profile submitted succesfully.'));

                return $this->redirectToRoute('account-index');
         
            }

            return $this->render('Professional/ProProfile_signup.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
                'customertype' => $customertype,
                'PortfolioActivate' => $PortfolioActivate
            ]);
            
        }

        return $this->render('Professional/NotLogged_signup.html.twig', [
             
        ]);
    }


    /**
     * 
     *
     * @Route("/get-listed", name="get-listed")
     * @param Request $request
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function getListed(Request $request, Security $security, Translator $translator, PaginatorInterface $paginator)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            
            $form = $this->createForm(GetListedFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $formData = $form->getData();
                $customertype = $form->get('customertype')->getData();
                $customer->setcustomertype($form->get('customertype')->getData());
                $customer->save();


                // Redirect based on selected customer type
                if ($customertype === 'Professional') {
                    return $this->redirect('/professionals/signup'); // Or use direct URL '/professionals/signup'
                } elseif ($customertype === 'Supplier') {
                    return $this->redirect('/suppliers/signup'); // Or use direct URL '/suppliers/signup'
                }
            }


            return $this->render('Professional/get-listed.html.twig', [
                'form' => $form->createView(),
            ]);
        
            }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/api/professional-signup/{customerId}", name="professional_signup_api", methods={"POST"})
     */
    public function professionalSignupAction(Request $request, $customerId): JsonResponse
    {
        try {
            // Find the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerId);
            $customersList = $customers->load();

            if (empty($customersList)) {
                // $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];

            // Get form data from request
            $formData = $request->request->all();
            $files = $request->files->all();

            // Create new ProProfile
            $proProfile = new ProProfile();
            $proProfile->setParent(Service::createFolderByPath('/Services/'.$formData['profession'].'s/Profiles'));
            
            // Set customer
            $proProfile->setCustomer($customer);

            // Handle profile image upload
            if (isset($files['ProfileImage'])) {
                $imageFile = $files['ProfileImage'];
                $imageName = $imageFile->getClientOriginalName();
                $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                
                $newAsset = new Image();
                $newAsset->setFilename($imageName);
                $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/".$formData['profession']."s/ProfileGallery"));
                $newAsset->setData(file_get_contents($imageFile->getPathname()));
                $newAsset->save();
                
                $proProfile->setProfileImage($newAsset);
            }

            // Set basic profile information
            $proProfile->setKey(Text::toUrl($formData['companyName'] . '-' . time()));
            $proProfile->setCompanyName($formData['companyName']);
            $proProfile->setProfessionalSupplierType($formData['profession']); // Assuming this is always Professional for the app
            $proProfile->setPortfolioType($formData['profession']);
            
            // Set professional details
            if (!empty($formData['description'])) {
                $proProfile->setDescription($formData['description']);
            }
            
            if (!empty($formData['specialization'])) {
                $proProfile->setSpecialization($formData['specialization']);
            }
            
            if (!empty($formData['gstNumber'])) {
                $proProfile->setGstnumber($formData['gstNumber']);
            }
            
            if (!empty($formData['yearEstablished'])) {
                $proProfile->setYearEstablished($formData['yearEstablished']);
            }
            
            if (!empty($formData['priceForHour'])) {
                $proProfile->setPriceForHour($formData['priceForHour']);
            }
            
            // Handle skills (coming as array from React Native)
            if (!empty($formData['skills'])) {
                $skills = is_array($formData['skills']) ? $formData['skills'] : explode(',', $formData['skills']);
                $proProfile->setSkills(implode(',', $skills));
            }
            
            // Handle cities served (coming as array from React Native)
            if (!empty($formData['citiesServed'])) {
                $citiesServed = is_array($formData['citiesServed']) ? $formData['citiesServed'] : explode(',', $formData['citiesServed']);
                $proProfile->setCitiesServed(implode(',', $citiesServed));
            }
            
            // Set contact information
            if (!empty($formData['countryCode'])) {
                $proProfile->setCountryCode($formData['countryCode']);
            }
            
            if (!empty($formData['phoneNumber'])) {
                $proProfile->setPhoneNumber($formData['phoneNumber']);
            }
            
            if (!empty($formData['streetAddress'])) {
                $proProfile->setStreetAddress($formData['streetAddress']);
            }
            
            if (!empty($formData['city'])) {
                $proProfile->setCity($formData['city']);
            }
            
            if (!empty($formData['state'])) {
                $proProfile->setState($formData['state']);
            }
            
            if (!empty($formData['country'])) {
                $proProfile->setCountry($formData['country']);
            }
            
            if (!empty($formData['pinCode'])) {
                $proProfile->setPinCode($formData['pinCode']);
            }
            
            $proProfile->setPublished(true);
            $proProfile->save();
            
            // Update customer portfolio status
            $customer->setPortfolioActivate('true');
            $customer->save();
            
            // Create notification
            $notification = new ProNotification();
            $notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $notification->setKey(Text::toUrl(time()));
            $notification->setMessage("Profile Details Submitted Successfully for Approval. You can sit back and relax!");
            $notification->setDescription("You will be notified once the review is completed.");
            $notification->setCustomer($customer);
            $notification->setUrl('javascript:void(0);');
            $notification->setPublished(true);
            $notification->save();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Professional profile created successfully',
                'profileId' => $proProfile->getId()
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error creating professional profile: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/supplier-signup/{customerId}", name="Supplier_signup_api", methods={"POST"})
     */
    public function supplierSignupAction(Request $request, $customerId): JsonResponse
    {
        try {
            // Find the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerId);
            $customersList = $customers->load();

            if (empty($customersList)) {
                // $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];

            // Get form data from request
            $formData = $request->request->all();
            $files = $request->files->all();

            // Create new ProProfile
            $proProfile = new ProProfile();
            $proProfile->setParent(Service::createFolderByPath('/Services/'.$formData['profession'].'s/Profiles'));
            
            // Set customer
            $proProfile->setCustomer($customer);

            // Handle profile image upload
            if (isset($files['ProfileImage'])) {
                $imageFile = $files['ProfileImage'];
                $imageName = $imageFile->getClientOriginalName();
                $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                
                $newAsset = new Image();
                $newAsset->setFilename($imageName);
                $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/".$formData['profession']."s/ProfileGallery"));
                $newAsset->setData(file_get_contents($imageFile->getPathname()));
                $newAsset->save();
                
                $proProfile->setProfileImage($newAsset);
            }

            // Set basic profile information
            $proProfile->setKey(Text::toUrl($formData['companyName'] . '-' . time()));
            $proProfile->setCompanyName($formData['companyName']);
            $proProfile->setProfessionalSupplierType($formData['profession']); // Assuming this is always Professional for the app
            $proProfile->setPortfolioType($formData['profession']);
            
            // Set professional details
            if (!empty($formData['description'])) {
                $proProfile->setDescription($formData['description']);
            }
            
            // if (!empty($formData['specialization'])) {
            //     $proProfile->setSpecialization($formData['specialization']);
            // }
            
            if (!empty($formData['gstNumber'])) {
                $proProfile->setGstnumber($formData['gstNumber']);
            }
            
            if (!empty($formData['yearEstablished'])) {
                $proProfile->setYearEstablished($formData['yearEstablished']);
            }
            
            // if (!empty($formData['priceForHour'])) {
            //     $proProfile->setPriceForHour($formData['priceForHour']);
            // }
            
            // Handle skills (coming as array from React Native)
            // if (!empty($formData['skills'])) {
            //     $skills = is_array($formData['skills']) ? $formData['skills'] : explode(',', $formData['skills']);
            //     $proProfile->setSkills(implode(',', $skills));
            // }
            
            // // Handle cities served (coming as array from React Native)
            // if (!empty($formData['citiesServed'])) {
            //     $citiesServed = is_array($formData['citiesServed']) ? $formData['citiesServed'] : explode(',', $formData['citiesServed']);
            //     $proProfile->setCitiesServed(implode(',', $citiesServed));
            // }
            
            // Set contact information
            if (!empty($formData['countryCode'])) {
                $proProfile->setCountryCode($formData['countryCode']);
            }
            
            if (!empty($formData['phoneNumber'])) {
                $proProfile->setPhoneNumber($formData['phoneNumber']);
            }
            
            if (!empty($formData['streetAddress'])) {
                $proProfile->setStreetAddress($formData['streetAddress']);
            }
            
            if (!empty($formData['city'])) {
                $proProfile->setCity($formData['city']);
            }
            
            if (!empty($formData['state'])) {
                $proProfile->setState($formData['state']);
            }
            
            if (!empty($formData['country'])) {
                $proProfile->setCountry($formData['country']);
            }
            
            if (!empty($formData['pinCode'])) {
                $proProfile->setPinCode($formData['pinCode']);
            }
            
            $proProfile->setPublished(true);
            $proProfile->save();
            
            // Update customer portfolio status
            $customer->setPortfolioActivate('true');
            $customer->save();
            
            // Create notification
            $notification = new ProNotification();
            $notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $notification->setKey(Text::toUrl(time()));
            $notification->setMessage("Profile Details Submitted Successfully for Approval. You can sit back and relax!");
            $notification->setDescription("You will be notified once the review is completed.");
            $notification->setCustomer($customer);
            $notification->setUrl('javascript:void(0);');
            $notification->setPublished(true);
            $notification->save();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Professional profile created successfully',
                'profileId' => $proProfile->getId()
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error creating professional profile: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    
    /**
     * @Route("/api/check-portfolio-status/{customerID}", name="check-portfolio-status", methods={"GET"})
     */
    public function checkPortfolioStatusAction($customerID, Request $request): JsonResponse
    {
        try {

            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                // $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            // $logger->info("Customer found: " . print_r($customer, true));
            // $logger->info("Customer class: " . get_class($customer));
            
            if (!$customer) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'portfolioActivated' => $customer->getPortfolioActivate() === 'true',
                'customerType' => $customer->getCustomerType()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @Route("/dealers/listing", name="Dealer-Listing")
     */
    public function DealerlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {   
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Dealer");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load ProProfiles
        $ProProfiles = $ProProfileList->load();

        // Prepare sortable array
        $sortableProfiles = [];
        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true); // Include inherited data
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Dealer Rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract the sorted objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // Paginate
        $pagination = $paginator->paginate(
            $sortedProProfiles,
            $request->query->getInt('page', 1),
            10
        );
        $paginationVariables = $pagination->getPaginationData();

        // Render template
        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'customertype' => 'Dealer',
            'filterform' => '1',
            'form' => $form->createView(),
            'paginationVariables' => $paginationVariables,
        ]);
    }

       




    /**
     * @Route("/contractors/listing", name="Contractor-Listing")
     */
    public function ContractorlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {   
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Contractor");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load and hydrate ProProfiles
        $ProProfiles = $ProProfileList->load();

        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true);
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort DESC by rating
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        $sortedProProfiles = array_column($sortableProfiles, 'object');

        $pagination = $paginator->paginate(
            $sortedProProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'customertype' => 'Contractor',
            'form' => $form->createView(),
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }

        



    /**
     * @Route("/architects/listing", name="Architect-Listing")
     */
    public function ArchitectlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Architect");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load and ensure all objects are fully hydrated
        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true); // Important if using inherited data
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                // Log and skip any profiles that fail
                $logger->error('Rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // Merge with raw DB architects if needed
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM Architect";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $architects = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Optional: skip merging if raw rows are not needed in sorting
        // Merge if needed, but raw array will not support CalculatedRating
        $finalProfiles = array_merge($sortedProProfiles, $architects);

        $pagination = $paginator->paginate(
            $finalProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/ProProfile_Listing_Cart_Architects.html.twig', [
            'form' => $form->createView(),
            'ProProfiles' => $pagination,
            'customertype' => 'Architect',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
        
    }


    /**
     * @Route("/earth-movers/listing", name="Earth-Mover-Listing")
     */
    public function EarthMoverlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        // // Fetch ArchitectProfiles objects
        // $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        // $ProProfileList->addConditionParam("PortfolioType = ?", "Architect");
        

        // $ProProfiles = $ProProfileList->load();
        

        $form = $this->createForm(FilterFormType::class);
        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $formData = $form->getData();
        //     $FilterCity = $form->get('FilterCity')->getData();
        //     $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        //     $ProProfileList->addConditionParam("PortfolioType = ?", "Architect");
            
        //     $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        //     $ProProfiles = $ProProfileList->load();
        // }

        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
        $sql = "SELECT * FROM earth_movers";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $architects = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ProProfiles = $architects;

        
        $pagination = $paginator->paginate(
            $ProProfiles,
            $request->query->getInt('page', 1),
            10  // Number of items per page
        );
        $paginationVariables = $pagination->getPaginationData();


        // Render the template with the architect profiles
        return $this->render('Professional/ProProfile_Listing_Cart_Architects.html.twig', [
            'form' => $form->createView(),
            'ProProfiles' => $pagination,
            'customertype' => 'EarthMovers',
            'filterform' => '1',
            'paginationVariables' => $paginationVariables,
        ]);
    }


    /**
     * @Route("/designers/listing", name="Designers-Listing")
     */
    public function DesignerlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
    
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Designer");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load the filtered profiles
        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true); // For inherited fields
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Rating fetch failed for Designer: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // Paginate
        $pagination = $paginator->paginate(
            $sortedProProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Designer',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }

        


    /**
     * @Route("/builders/listing", name="Builders-Listing")
     */
    public function BuilderlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Builder");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load Builder Profiles
        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true); // Ensure inherited fields are loaded
                $rating = (float) $profile->getCalculatedRating();       // Cast to float to ensure sorting
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // Merge with Structural DB entries (if applicable)
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM Structural";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $structural = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $finalProfiles = array_merge($sortedProProfiles, $structural);

        // Paginate final result
        $pagination = $paginator->paginate(
            $finalProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/ProProfile_Listing_Cart_Architects.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Builder',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }

        



    /**
     * @Route("/manufacturers/listing", name="Manufacturers-Listing")
     */
    public function ManufacturerlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        // ---- 1. Load Pimcore Manufacturer Objects ----
        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Manufacturer");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load and ensure all objects are fully hydrated
        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true);
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Manufacturer rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // ---- 2. Load Supplier Table Data ----
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM supplier";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Merge ProProfile manufacturers with database suppliers
        $finalProfiles = array_merge($sortedProProfiles, $suppliers);

        $pagination = $paginator->paginate(
            $finalProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/ProProfile_Listing_Cart_Manufacturers.html.twig', [
            'form' => $form->createView(),
            'ProProfiles' => $pagination,
            'customertype' => 'Manufacturer',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }


        


    /**
     * @Route("/distributors/listing", name="Distributors-Listing")
     */
    public function DistributorlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Distributor");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load and ensure all objects are fully hydrated
        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true); // Important if using inherited data
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        $pagination = $paginator->paginate(
            $sortedProProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Distributor',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }

        
    


    /**
     * @Route("/retailers/listing", name="Retailer-Listing")
     */
    public function RetailerlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Retailer");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        // Load and hydrate
        $ProProfiles = $ProProfileList->load();

        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true);
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Rating fetch failed (Retailer): ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        $pagination = $paginator->paginate(
            $sortedProProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/professional_listing.html.twig', [
            'ProProfiles' => $pagination,
            'form' => $form->createView(),
            'customertype' => 'Retailer',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
        ]);
    }

        


    /**
     * @Route("/engineers/listing", name="Engineers-Listing")
     */
    public function EngineerlistingAction(Request $request, LoggerInterface $logger, Security $security, PaginatorInterface $paginator)
    {
        

        $ProProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        $ProProfileList->addConditionParam("PortfolioType = ?", "Engineer");

        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FilterCity = $form->get('FilterCity')->getData();
            $ProProfileList->addConditionParam("FIND_IN_SET(?, CitiesServed) > 0", $FilterCity);
        }

        $ProProfiles = $ProProfileList->load();

        // Build sortable array
        $sortableProfiles = [];

        foreach ($ProProfiles as $profile) {
            try {
                \Pimcore\Model\DataObject::setGetInheritedValues(true);
                $rating = (float) $profile->getCalculatedRating();
                $sortableProfiles[] = [
                    'rating' => $rating,
                    'object' => $profile
                ];
            } catch (\Throwable $e) {
                $logger->error('Engineer rating fetch failed: ' . $e->getMessage());
            }
        }

        // Sort by rating DESC
        usort($sortableProfiles, fn($a, $b) => $b['rating'] <=> $a['rating']);

        // Extract sorted ProProfile objects
        $sortedProProfiles = array_column($sortableProfiles, 'object');

        // Load Engineer raw data from DB (if needed)
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM Engineer";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $engineers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $finalProfiles = array_merge($sortedProProfiles, $engineers);

        $pagination = $paginator->paginate(
            $finalProfiles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Professional/ProProfile_Listing_Cart_Architects.html.twig', [
            'form' => $form->createView(),
            'ProProfiles' => $pagination,
            'customertype' => 'Engineer',
            'filterform' => '1',
            'paginationVariables' => $pagination->getPaginationData(),
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
            
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $customertype = $customer->getcustomertype();
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
                            $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
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
            $form = $this->createForm(ProfessionalAddProjectFormType::class, null, [
                'is_builder' => false
            ]);
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
                    $videoPaths = [];

                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Contractors/ProjectGallery"));
                                $image->save();
                                $hotspotImage->setImage($image);
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }

                    // Set image gallery
                    if (!empty($items)) {
                        $ProProject->setProjectGallery(new ImageGallery($items));
                    }
                    
                    // Set video paths (using pipe delimiter)
                    if (!empty($videoPaths)) {
                        $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                    }
                    
                    $ProProject->setPublished(true);
                    $ProProject->save();

                    $this->addFlash('success', $translator->trans('Project submitted successfully.'));
                    return $this->redirectToRoute('account-Projects');
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'ProProfile' => $ProProfile,
                    'is_builder' => true,
                ]);
            }
        }

        return $this->render('Professional/NotLogged_signup.html.twig');
    }



    /**
    * @Route("/professional/portfolio/add-project", name="Professional-Add-Project")
    */
    public function ProfessionalProjectSubmitAction(Request $request, Security $security, Translator $translator)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $form = $this->createForm(ProfessionalAddProjectFormType::class);
            $form->handleRequest($request);
            if ($PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProject = new ProProject();
                    $ProProject->setParent(Service::createFolderByPath('/Services/Professionals/Projects'));
        
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
                            $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                            $image = new Image();
                            $image->setFilename($imageName);
                            $image->setData(file_get_contents($file->getPathname()));
                            $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Professionals/ProjectGallery"));
                            $image->save();
                            $hotspotImage->setImage($image);
                        }

                        $items[] = $hotspotImage;
                    }

                    $ProProject->setProjectGallery(new ImageGallery($items));    
                    $ProProject->setPublished(true);    
                    $ProProject->save();
    
                    $this->addFlash('success', $translator->trans('Project submitted succesfully.'));
                    
                    return $this->redirectToRoute('account-Projects');
                    // return $this->render('Professional/professional_projectForm_success.html.twig', ['ArchitectProject' => $ProProject, 'ArchitectProfile' => $ProProfile, 'customertype' => $customertype, 'customer' => $customer,]);
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
            $form = $this->createForm(ProfessionalAddProjectFormType::class, null, [
                'is_builder' => false
            ]);
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
                    $videoPaths = [];

                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                                $image->save();
                                $hotspotImage->setImage($image);
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }

                    // Set image gallery
                    if (!empty($items)) {
                        $ProProject->setProjectGallery(new ImageGallery($items));
                    }
                    
                    // Set video paths (using pipe delimiter)
                    if (!empty($videoPaths)) {
                        $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                    }
                    
                    $ProProject->setPublished(true);
                    $ProProject->save();

                    $this->addFlash('success', $translator->trans('Project submitted successfully.'));
                    return $this->redirectToRoute('account-Projects');
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'is_builder' => true,
                ]);
            }
        }

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
            $form = $this->createForm(ProfessionalAddProjectFormType::class, null, [
                'is_builder' => false
            ]);
            $form->handleRequest($request);
            
            if ($PortfolioActivate === 'true') {
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
                    $videoPaths = [];

                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Designers/ProjectGallery"));
                                $image->save();
                                $hotspotImage->setImage($image);
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }

                    // Set image gallery
                    if (!empty($items)) {
                        $ProProject->setProjectGallery(new ImageGallery($items));
                    }
                    
                    // Set video paths as serialized array in text field
                    if (!empty($videoPaths)) {
                        // $ProProject->setProjectVideoPaths(serialize($videoPaths));
                        $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                    }
                    
                    $ProProject->setPublished(true);
                    $ProProject->save();

                    $this->addFlash('success', $translator->trans('Project submitted successfully.'));
                    return $this->redirectToRoute('account-index');
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'is_builder' => false, 
                ]);
            }
        }

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
            $form = $this->createForm(ProfessionalAddProjectFormType::class, null, [
                'is_builder' => true
            ]);
            
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
                    
                    // Set project category if builder
                    $ProProject->setProjectCategory($form->get('ProjectCategory')->getData());
                    
                    $ProProject->setMinPrice($form->get('PriceRange')->getData());
                    $ProProject->setConfiguration($form->get('Configuration')->getData());
                    $ProProject->setCollaborations($form->get('Collaborations')->getData());
                    $ProProject->setProfessionalPath($ProProfile);

                    $galleryData = $form->get('ProjectGallery')->getData();
                    $items = [];
                    $videoPaths = [];

                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Builders/ProjectGallery"));
                                $image->save();
                                $hotspotImage->setImage($image);
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }

                    // Set image gallery
                    if (!empty($items)) {
                        $ProProject->setProjectGallery(new ImageGallery($items));
                    }
                    
                    // Set video paths (using pipe delimiter)
                    if (!empty($videoPaths)) {
                        $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                    }
                    
                    $ProProject->setPublished(true);
                    $ProProject->save();

                    $this->addFlash('success', $translator->trans('Project submitted successfully.'));
                    return $this->redirectToRoute('account-Projects');
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'is_builder' => true, 
                ]);
            }
        }

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
            $form = $this->createForm(ProfessionalAddProjectFormType::class, null, [
                'is_builder' => false
            ]);
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
                    $videoPaths = [];

                    foreach ($galleryData as $file) {
                        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            // Handle images
                            if (strpos($file->getMimeType(), 'image/') === 0) {
                                $hotspotImage = new Hotspotimage();
                                $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $image = new Image();
                                $image->setFilename($imageName);
                                $image->setData(file_get_contents($file->getPathname()));
                                $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Engineers/ProjectGallery"));
                                $image->save();
                                $hotspotImage->setImage($image);
                                $items[] = $hotspotImage;
                            }
                            // Handle videos
                            elseif (strpos($file->getMimeType(), 'video/') === 0) {
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($file->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            }
                        }
                    }

                    // Set image gallery
                    if (!empty($items)) {
                        $ProProject->setProjectGallery(new ImageGallery($items));
                    }
                    
                    // Set video paths (using pipe delimiter)
                    if (!empty($videoPaths)) {
                        $ProProject->setProjectVideoPaths(implode('|', $videoPaths));
                    }
                    
                    $ProProject->setPublished(true);
                    $ProProject->save();

                    $this->addFlash('success', $translator->trans('Project submitted successfully.'));
                    return $this->redirectToRoute('account-Projects');
                }
                
                return $this->render('Professional/professional_add_project.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'is_builder' => true,
                ]);
            }
        }

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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'designer',
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/professional/project/{url}", name="Professional_project_single")
     */
    public function ProfessionalProjectDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load DesignerProfile based on the URL
        $ProProject = ProProject::getByPath("/Services/Professionals/Projects/$url");

        if (!$ProProject) {
            throw $this->createNotFoundException('Project not found');
        }

        $ProProfile = $ProProject->getProfessional();
        $customer = $ProProfile->getCustomer();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/professional/Enquiries'));

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProject,
            'listingtype' => 'Builder',
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/contractor/portfolio/{url}", name="Contract_profile")
     */
    public function ContractorProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Contractors/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }

            $customer = $ProProfile->getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();
        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProjects' => $numberOfProjects,
                'customer' => $customer,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/engineer/portfolio/{url}", name="Engineer_profile")
     */
    public function EngineerProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

        
            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Engineers/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }
            $customer  = $ProProfile->getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();
        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            if ($url === 'Sambath-Engineers-1753428110') {
                    return $this->render('Professional/sambath_professional_profile.html.twig', [
                    'architectProfile' => $ProProfile,
                    'numberOfProjects' => $numberOfProjects,
                    'form' => $form->createView(),
                    'customer' => $customer,
                    'creationdate' =>  $formattedCreationDate,
                    'pagination' => $pagination,
                    'paginationVariables' => $paginationVariables,
                    'reviews' => $ProProfile->getProRatings(),
                ]);
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProjects' => $numberOfProjects,
                'form' => $form->createView(),
                'customer' => $customer,
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }

        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/{CustomerType}/product/{url}", name="Manufacturer_product_single")
     */
    public function ManufacturerProductDetails($url, $CustomerType, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProduct = ProProduct::getByPath("/Services/".ucfirst($CustomerType)."s/Products/$url");

        if (!$ProProduct) {
            $ProProduct = ProProduct::getByPath("/Services/Suppliers/Products/$url");
            
            if (!$ProProduct) {
                throw $this->createNotFoundException('Product not found');
            }
        }

        // Update page views count
        $currentViews = $ProProduct->getPageViewsCount();
        if (empty($currentViews)) {
            $ProProduct->setPageViewsCount(1); // Initialize with 1 if empty or null
        } else {
            $ProProduct->setPageViewsCount($currentViews + 1); // Increment by 1
        }
        $ProProduct->save();


        $ProProfile = $ProProduct->getProfessional();
        $customer = $ProProfile->getCustomer();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath("/Services/".ucfirst($CustomerType)."/Enquiries"));

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        
        // Fetch and paginate reviews
        $reviews = $ProProduct->getProductReviews();
        $pagination = $paginator->paginate(
            $reviews,
            $request->query->getInt('page', 1),
            5 // 5 reviews per page
        );

        // Calculate average rating
        $averageRating = 0;
        $totalReviews = count($reviews);
        if ($totalReviews > 0) {
            $totalRating = 0;
            foreach ($reviews as $review) {
                $totalRating += $review->getRating();
            }
            $averageRating = round($totalRating / $totalReviews, 1);
        }

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => $ProProfile->getPortfolioType(),
            'form' => $form->createView(),
            'metadescription' => $ProProduct->getProductDescription() ? substr($ProProduct->getProductDescription(), 0, 160) : 'Product details from ' . $ProProfile->getCompanyName(),
            'reviews' => $pagination,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }


    /**
     * @Route("/dealer/product/{url}", name="Dealer_product_single")
     */
    public function dealerProductDetails($url, Request $request, PaginatorInterface $paginator)
    {
       
        // Load ArchitectProfile based on the URL
        $ProProduct = ProProduct::getByPath("/Services/Dealers/Products/$url");

        if (!$ProProduct) {
            throw $this->createNotFoundException('Product not found');
        }

        $ProProfile = $ProProduct->getProfessional();
        $customer = $ProProfile->getCustomer();

        $form = $this->createForm(ProEnquiryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath('/Services/Dealers/Enquiries'));

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => $ProProfile->getPortfolioType(),
            'form' => $form->createView(),
            'metadescription' => $ProProduct->getProductDescription() ? substr($ProProduct->getProductDescription(), 0, 160) : 'Product details from ' . $ProProfile->getCompanyName(),
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => $ProProfile->getPortfolioType(),
            'form' => $form->createView(),
            'metadescription' => $ProProduct->getProductDescription() ? substr($ProProduct->getProductDescription(), 0, 160) : 'Product details from ' . $ProProfile->getCompanyName(),
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
        $customer = $ProProfile->getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }
        

        return $this->render('Professional/ProProductSinglePage.html.twig', [
            'architectProfile' => $ProProfile,
            'ProProject' => $ProProduct,
            'listingtype' => $ProProfile->getPortfolioType(),
            'form' => $form->createView(),
            'metadescription' => $ProProduct->getProductDescription() ? substr($ProProduct->getProductDescription(), 0, 160) : 'Product details from ' . $ProProfile->getCompanyName(),
        ]);
    }


    /**
     * @Route("/designer/portfolio/{url}", name="designer_profile")
     */
    public function DesignerProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Designers/Profiles/$url");

            $customer = $ProProfile->getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();
        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProjects' => $numberOfProjects,
                'customer' => $customer,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }

        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/architect/portfolio/{url}", name="Architect_profile")
     */
    public function ArchitectsProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
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
            
            // Email
            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();

            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
            

    
            $this->addFlash('success', 'Enquiry submitted succesfully.');
        }

        return $this->render('Professional/professional_profile.html.twig', [
            'architectProfile' => $ProProfile,
            'numberOfProjects' => $numberOfProjects,
            'customer' => $customer,
            'form' => $form->createView(),
            'creationdate' =>  $formattedCreationDate,
            'pagination' => $pagination,
            'paginationVariables' => $paginationVariables,
            'reviews' => $ProProfile->getProRatings(),
        ]);
        
    }

    /**
     * @Route("/builder/portfolio/{url}", name="Builder_profile")
     */
    public function BuildersProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

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

                // if ($form->get('City')->getData()) {
                //     $ProEnquiry->setCity($form->get('City')->getData());
                // }

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
                'customer' => $customer,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'requirements' => $selectedRequirements,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }

        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');

    }


    /**
     * @Route("/manufacturer/portfolio/{url}", name="Manufacturer_profile")
     */
    public function ManufacturersProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Manufacturers/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }
            $customer = $ProProfile->getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();

        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProducts' => $numberOfProducts,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }

        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    
    /**
     * @Route("/distributor/portfolio/{url}", name="Distributor_profile")
     */
    public function DistributorProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Distributors/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }
            
            $customer = $ProProfile->getCustomer();
            
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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();

        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProducts' => $numberOfProducts,
                'form' => $form->createView(),
                'customer' => $customer,
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/retailer/portfolio/{url}", name="Retailer_profile")
     */
    public function RetailerProfileAction($url, Request $request, Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Retailers/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }

            $customer = $ProProfile->getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();

        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProducts' => $numberOfProducts,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'customer' => $customer,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }
    

    /**
     * @Route("/dealer/portfolio/{url}", name="Dealer_profile")
     */
    public function DealerProfileAction($url, Request $request,  Security $security, PaginatorInterface $paginator)
    {   
        $user = $security->getUser();
        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProProfile = ProProfile::getByPath("/Services/Dealers/Profiles/$url");

            if (!$ProProfile) {
                throw $this->createNotFoundException('profile not found');
            }

            $customer = $ProProfile->getCustomer();

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
                $ProEnquiry->setParent(Service::createFolderByPath('/Services/Dealers/Enquiries'));
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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();

        
                $this->addFlash('success', 'Enquiry submitted succesfully.');
            }

            return $this->render('Professional/professional_profile.html.twig', [
                'architectProfile' => $ProProfile,
                'numberOfProducts' => $numberOfProducts,
                'form' => $form->createView(),
                'creationdate' =>  $formattedCreationDate,
                'pagination' => $pagination,
                'customer' => $customer,
                'paginationVariables' => $paginationVariables,
                'reviews' => $ProProfile->getProRatings(),
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');

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

                // $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                // $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryUnlockedEmail");
                // $EmailTemplate = $EmailTemplates->load();
                // $EmailTemplate = $EmailTemplate[0];

                // $subject = $EmailTemplate->getSubject();
                // $htmlContent = $EmailTemplate->getContent();
                // eval("\$htmlContent = \"$htmlContent\";");
                // // Create a new Pimcore\Mail instance
                // $mail = new \Pimcore\Mail();
                // // $mail->from('arqonztest@gmail.com');
                // $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                // $mail->to($customer->getEmail());
                // $mail->subject($subject);
                // $mail->html($htmlContent);
                // $mail->send();
                return $this->render('Professional/dashboard_unlock_enquiry_success.html.twig', [
                    'enquiryLink' => $enquiryLink,
                    'customer' => $customer,
                    'ProProfile' => $ProProfile,
                ]);

            }else{
                return $this->render('Professional/dashboard_unlock_enquiry_failed.html.twig', [
                    'customer' => $customer,
                    'ProProfile' => $ProProfile,
                ]);
            }
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/account/pricing", name="Pricing-page")
    */
    public function PricingPageAction(Request $request, Security $security, PaginatorInterface $paginator, \App\Service\CurrencyConversionService $currencyService)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            
            // Check if annual fee should be applied
            $showAnnualFee = false;
            $annualFeeExpiry = null;

            $subscriptionStart = $customer->getSubscriptionStart();
            
            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    $showAnnualFee = true;
                } else {
                    $annualFeeExpiry = $oneYearAfterSubscription;
                }
            } else {
                // First time user, show annual fee
                $showAnnualFee = true;
            }

            // Determine if customer is from India
            $phoneCountry = $customer->getPhoneCountry();
            $isIndianCustomer = $currencyService->isIndianCustomer($phoneCountry);
            
            // Get currency information
            $currencySymbol = $currencyService->getCurrencySymbol($phoneCountry);
            $currencyCode = $currencyService->getCurrencyCode($phoneCountry);
            $taxLabel = $currencyService->getTaxLabel($phoneCountry);
            
            // Calculate pricing based on customer location
            $pricingData = $this->calculatePricingData($currencyService, $isIndianCustomer, $showAnnualFee);

            return $this->render('Professional/dashboard_pricing.html.twig', [
                'customer' => $customer,
                'ProProfile' => $ProProfile,
                'showAnnualFee' => $showAnnualFee,
                'annualFeeExpiry' => $annualFeeExpiry,
                'isIndianCustomer' => $isIndianCustomer,
                'currencySymbol' => $currencySymbol,
                'currencyCode' => $currencyCode,
                'taxLabel' => $taxLabel,
                'pricingData' => $pricingData,
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    /**
     * Calculate pricing data based on customer location and currency
     */
    private function calculatePricingData(\App\Service\CurrencyConversionService $currencyService, bool $isIndianCustomer, bool $showAnnualFee): array
    {
        // Base prices in INR
        $basePrices = [
            'Silver' => 1500,
            'Gold' => 3000,
            'Platinum' => 6000,
            'AnnualFee' => 500
        ];

        $pricingData = [];

        foreach ($basePrices as $plan => $inrPrice) {
            if ($isIndianCustomer) {
                // For Indian customers, keep INR pricing
                $pricingData[$plan] = [
                    'basePrice' => $inrPrice,
                    'taxAmount' => $inrPrice * 0.18,
                    'totalPrice' => $inrPrice + ($inrPrice * 0.18),
                    'currencySymbol' => '₹',
                    'currencyCode' => 'INR',
                    'taxLabel' => 'GST'
                ];
            } else {
                // For non-Indian customers, convert to USD
                $usdPrice = $currencyService->convertInrToUsd($inrPrice);
                $pricingData[$plan] = [
                    'basePrice' => $usdPrice,
                    'taxAmount' => $usdPrice * 0.18, // Same tax rate as GST
                    'totalPrice' => $usdPrice + ($usdPrice * 0.18),
                    'currencySymbol' => '$',
                    'currencyCode' => 'USD',
                    'taxLabel' => 'VAT'
                ];
            }
        }

        return $pricingData;
    }

    /**
     * @Route("/create-unlock-order", name="create_unlock_order", methods={"POST"})
     */
    public function createUnlockOrder(Request $request, Security $security)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $amount = 10000; // ₹100 in paise
        
        $razorpayConfig = \App\Service\EnvironmentConfigService::getRazorpayConfig();
        $razorpayKey = $razorpayConfig['key_id'];
        $razorpaySecret = $razorpayConfig['key_secret'];
        
        if (empty($razorpayKey) || empty($razorpaySecret)) {
            throw new \Exception('Razorpay API credentials not configured');
        }
        
        $api = new Api($razorpayKey, $razorpaySecret);

        try {
            $orderData = [
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => 'unlock_' . time(),
                'payment_capture' => 1
            ];
            $razorpayOrder = $api->order->create($orderData);
            return $this->json([
                'orderId' => $razorpayOrder['id'],
                'amount' => $amount,
                'razorpayKey' => $razorpayKey,
                'name' => 'Arqonz Global Pvt. Ltd.',
                'description' => "One-time Enquiry Unlock",
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Razorpay order creation failed'], 500);
        }
    }


    /**
     * @Route("/verify-unlock-payment", name="verify_unlock_payment")
     */
    public function verifyUnlockPayment(Request $request, Security $security)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $razorpayConfig = \App\Service\EnvironmentConfigService::getRazorpayConfig();
        $razorpayKey = $razorpayConfig['key_id'];
        $razorpaySecret = $razorpayConfig['key_secret'];
        
        if (empty($razorpayKey) || empty($razorpaySecret)) {
            throw new \Exception('Razorpay API credentials not configured');
        }
        $api = new Api($razorpayKey, $razorpaySecret);

    

        try {
            $attributes = [
                'razorpay_signature' => $request->query->get('razorpay_signature'),
                'razorpay_payment_id' => $request->query->get('payment_id'),
                'razorpay_order_id' => $request->query->get('order_id')
            ];

            $api->utility->verifyPaymentSignature($attributes);
            
            // Get the enquiry details
            $enquirytype = $request->query->get('enquirytype');
            $url = $request->query->get('url');
            
            // Fetch the enquiry
            $ProEnquiry = ProEnquiry::getByPath("/Services/$enquirytype/Enquiries/$url");
            
            if ($ProEnquiry) {
                // Set the unlock status
                $ProEnquiry->setUnlock('true');
                $ProEnquiry->save();
                
                // Record the payment
                $timeZone = new \DateTimeZone('Asia/Kolkata');
                $currentDate = new \DateTime('now', $timeZone);
                
                $PaymentOrder = new PaymentOrder();
                $PaymentOrder->setParent(Service::createFolderByPath('/PaymentOrders'));
                $PaymentOrder->setKey(uniqid());
                $PaymentOrder->setProProfile($user->getPortfolio()[0]);
                $PaymentOrder->setSubscriptionPlan('OneTimeUnlock');
                $PaymentOrder->setOrderDate($currentDate);
                $PaymentOrder->save();
                
                // Redirect back to the enquiry page
                return $this->redirectToRoute('Enquiry-page', [
                    'enquirytype' => $enquirytype,
                    'url' => $url
                ]);
            }
            
            throw new \Exception('Invalid enquiry');
            
        } catch (\Exception $e) {
            // Handle error
            return $this->redirectToRoute('dashboard');
        }
    }

    /**
     * @Route("/bid-payment-verify", name="bid-payment-verify")
     */
    public function bidPaymentverify(Request $request, Security $security)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $razorpayConfig = \App\Service\EnvironmentConfigService::getRazorpayConfig();
        $razorpayKey = $razorpayConfig['key_id'];
        $razorpaySecret = $razorpayConfig['key_secret'];
        
        if (empty($razorpayKey) || empty($razorpaySecret)) {
            throw new \Exception('Razorpay API credentials not configured');
        }
        $api = new Api($razorpayKey, $razorpaySecret);

    

        try {
            $attributes = [
                'razorpay_signature' => $request->query->get('razorpay_signature'),
                'razorpay_payment_id' => $request->query->get('payment_id'),
                'razorpay_order_id' => $request->query->get('order_id')
            ];

            $api->utility->verifyPaymentSignature($attributes);
            
            

            
            if ($user) {
                $UserCredits = $user->getCreditPoints();
                $user->setCreditPoints($UserCredits + 1);
                $user->save();
                // Record the payment
                $timeZone = new \DateTimeZone('Asia/Kolkata');
                $currentDate = new \DateTime('now', $timeZone);
                
                $PaymentOrder = new PaymentOrder();
                $PaymentOrder->setParent(Service::createFolderByPath('/PaymentOrders'));
                $PaymentOrder->setKey(uniqid());
                $PaymentOrder->setProProfile($user->getPortfolio()[0]);
                $PaymentOrder->setSubscriptionPlan('OneTimeUnlock');
                $PaymentOrder->setOrderDate($currentDate);
                $PaymentOrder->save();
                
                // Redirect back to the enquiry page
                return $this->redirectToRoute('Account-Requirement-Details', [
                ]);
            }
            
            throw new \Exception('Invalid enquiry');
            
        } catch (\Exception $e) {
            // Handle error
            return $this->redirectToRoute('dashboard');
        }
    }





    /**
     * @Route("/create-order", name="create_order", methods={"POST"})
     */
    public function createOrder(Request $request, Security $security, \App\Service\CurrencyConversionService $currencyService)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $plan = $request->request->get('plan');
        $includeAnnualFee = $request->request->get('includeAnnualFee');
        
        // Determine if customer is from India
        $phoneCountry = $user->getPhoneCountry();
        $isIndianCustomer = $currencyService->isIndianCustomer($phoneCountry);
        $currencyCode = $currencyService->getCurrencyCode($phoneCountry);
        $taxLabel = $currencyService->getTaxLabel($phoneCountry);
        
        // Base amounts in INR (in paise)
        $baseAmountInr = 0;
        switch ($plan) {
            case 'Standard':
                $baseAmountInr = 50000;
                break;
            case 'Silver':
                $baseAmountInr = 150000;
                break;
            case 'Gold':
                $baseAmountInr = 300000;
                break;
            case 'Platinum':
                $baseAmountInr = 600000;
                break;
            default:
                return $this->json(['error' => 'Invalid plan selected'], 400);
        }
        
        // Add annual fee if applicable
        if ($includeAnnualFee == "true") {
            $baseAmountInr += 50000; // 500 Rs in paise
        }

        // Apply tax (18% GST for India, 18% VAT for others)
        $taxAmount = $baseAmountInr * 0.18;
        $totalAmountInr = $baseAmountInr + $taxAmount;
        $totalAmountInr = round($totalAmountInr); // Ensure we have a whole number

        // Convert to appropriate currency for payment
        if ($isIndianCustomer) {
            $paymentAmount = $totalAmountInr;
            $paymentCurrency = 'INR';
        } else {
            // For non-Indian customers, convert to USD (multiply by 100 for cents)
            $totalAmountUsd = $currencyService->convertInrToUsd($totalAmountInr / 100) * 100;
            $paymentAmount = round($totalAmountUsd);
            $paymentCurrency = 'USD';
        }

        $razorpayKey = 'rzp_live_lHiuTO7zDrXx97'; 
        $razorpaySecret = 'pnZHy4LlAVFM1DgRyZiMfBOg'; 
        $api = new Api($razorpayKey, $razorpaySecret);

        try {
            $orderData = [
                'amount' => $paymentAmount,
                'currency' => $paymentCurrency,
                'receipt' => 'order_' . time(),
                'payment_capture' => 1
            ];
            $razorpayOrder = $api->order->create($orderData);
            
            // Description should indicate if annual fee is included
            $description = "Payment for $plan Plan";
            if ($includeAnnualFee == "true") {
                $description .= " with Annual Subscription Fee";
            }
            $description .= " (Incl. 18% $taxLabel)";
            
            return $this->json([
                'orderId' => $razorpayOrder['id'],
                'amount' => $paymentAmount,
                'baseAmount' => $baseAmountInr,
                'taxAmount' => $taxAmount,
                'currency' => $paymentCurrency,
                'razorpayKey' => $razorpayKey,
                'name' => 'Arqonz Global Pvt. Ltd.',
                'description' => $description,
                'includeAnnualFee' => $includeAnnualFee,
                'customerName' => $user->getfirstname() . ' ' . $user->getlastname(),
                'customerEmail' => $user->getemail(),
                'customerPhone' => $user->getphone(),
                'isIndianCustomer' => $isIndianCustomer
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

            $razorpayConfig = \App\Service\EnvironmentConfigService::getRazorpayConfig();
            $razorpayKey = $razorpayConfig['key_id'];
            $razorpaySecret = $razorpayConfig['key_secret'];
            
            if (empty($razorpayKey) || empty($razorpaySecret)) {
                throw new \Exception('Razorpay API credentials not configured');
            }
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
        // // Temporary logging - remove after debugging
        // file_put_contents('/var/www/pimcore/var/log/payment_debug.log', 
        //     "Payment verification started: " . date('Y-m-d H:i:s') . "\n" .
        //     "Payment ID: " . $request->query->get('payment_id') . "\n" .
        //     "Order ID: " . $request->query->get('order_id') . "\n" .
        //     "Plan: " . $request->query->get('plan') . "\n" .
        //     "Include Annual Fee: " . $request->query->get('includeAnnualFee') . "\n\n",
        //     FILE_APPEND
        // );

        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $razorpayKey = 'rzp_live_lHiuTO7zDrXx97';
            $razorpaySecret = 'pnZHy4LlAVFM1DgRyZiMfBOg';
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
                    // $includeAnnualFee = $request->query->get('includeAnnualFee') === 'true';
                    $includeAnnualFee = $request->query->get('includeAnnualFee');
                    
                    
                    $PaymentOrder = new PaymentOrder();
                    $PaymentOrder->setParent(Service::createFolderByPath('/PaymentOrders'));
                    $PaymentOrder->setKey(uniqid());
                    $PaymentOrder->setProProfile($ProProfile);
                    
                    if ($paymentId) {
                        $currentCreditsPoints = $customer->getCreditPoints();
                        
                        if ($plan === 'Standard') {
                            $customer->setCreditPoints($currentCreditsPoints + 6);
                            $customer->setSubscriptionPlan('Standard');
                            $PaymentOrder->setSubscriptionPlan('Standard');
                        } elseif ($plan === 'Silver') {
                            $customer->setCreditPoints($currentCreditsPoints + 18); // Updated to 18 as per your requirement
                            $customer->setSubscriptionPlan('Silver');
                            $PaymentOrder->setSubscriptionPlan('Silver');
                        } elseif ($plan === 'Gold') {
                            $customer->setCreditPoints($currentCreditsPoints + 36); // Updated to 36 as per your requirement
                            $customer->setSubscriptionPlan('Gold');
                            $PaymentOrder->setSubscriptionPlan('Gold');
                        } elseif ($plan === 'Platinum') {
                            $customer->setCreditPoints($currentCreditsPoints + 72);
                            $customer->setSubscriptionPlan('Platinum');
                            $PaymentOrder->setSubscriptionPlan('Platinum');
                        }
                        
                        // Set subscription start date (which is used to determine when to show annual fee again)
                        $customer->setSubscriptionStart($currentCarbonDate);
                        
                        // Record if annual fee was paid
                        if ($includeAnnualFee == "true") {
                            $PaymentOrder->setHasAnnualFee("true");
                        }
                        
                        $PaymentOrder->setOrderDate($currentCarbonDate);
                        $PaymentOrder->setPublished(true);
                        $PaymentOrder->save();

                        $customer->save();
                    }

                    // Email notifications and other logic...
                    $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                    $EmailTemplates->addConditionParam("TemplateName = ?", "SubscriptionSuccessEmail");
                    $EmailTemplate = $EmailTemplates->load();
                    $EmailTemplate = $EmailTemplate[0];

                    $subject = $EmailTemplate->getSubject();
                    $htmlContent = $EmailTemplate->getContent();
                    $htmlContent = str_replace("[Professional Name]", $customer->getFirstName(), $htmlContent);
                    $htmlContent = str_replace("[MyPlan]", $plan, $htmlContent);
                
                    // Create a new Pimcore\Mail instance
                    $mail = new \Pimcore\Mail();
                    // $mail->from('arqonztest@gmail.com');
                    $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                    $mail->to($customer->getEmail());
                    $mail->subject($subject);
                    // $mail->html($htmlContent);
                    // $mail->send();



                    // In your VerifySubsAction, after the email is sent, add:
                    $invoiceResponse = $this->generateInvoice($PaymentOrder, $request, $security);
                    if ($invoiceResponse->isOk()) {
                        $invoiceData = json_decode($invoiceResponse->getContent(), true);
                        
                        // Attach invoice to the email
                        $invoiceAsset = Asset::getByPath($invoiceData['invoicePath']);
                        if ($invoiceAsset) {
                            $mail->attach(\Pimcore\Tool\Storage::get('asset')->readStream($invoiceAsset->getFullPath()), 
                                        'invoice.pdf', 
                                        'application/pdf');
                        }
                        
                        // Update email content to mention invoice
                        $htmlContent .= '<p>Please find attached your invoice for this transaction.</p>';
                        $mail->html($htmlContent);
                        $mail->send();
                    }
                    
                } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                    // Handle signature verification error
                    $unlock = false;
                }
            } else {
                $unlock = false;
            }
            return $this->render('Professional/dashboard_subscription_verify.html.twig', []);
            
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    // Add this function to your ContentController.php
    /**
     * @Route("/generate-invoice", name="generate_invoice")
     */
    public function generateInvoice($paymentOrder, Request $request, Security $security)
    {
        $user = $security->getUser();
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        // Get the latest payment order for this user
        // $paymentOrders = new \Pimcore\Model\DataObject\PaymentOrder\Listing();
        // $paymentOrders->addConditionParam("ProProfile__id = ?", $user->getId());
        // $paymentOrders->setOrderKey('o_creationDate');
        // $paymentOrders->setOrder('DESC');
        // $paymentOrders->setLimit(1);
        // $paymentOrder = $paymentOrders->current();

        if (!$paymentOrder) {
            return $this->json(['error' => 'No payment order found'], 404);
        }

        // Calculate amounts
        $plan = $paymentOrder->getSubscriptionPlan();
        $baseAmount = 0;
        $planbaseAmt = 0;
        $includeAnnualFee = $paymentOrder->getHasAnnualFee();

        switch ($plan) {
            case 'Standard':
                $baseAmount = 500;
                break;
            case 'Silver':
                $baseAmount = 1500;
                break;
            case 'Gold':
                $baseAmount = 3000;
                break;
            case 'Platinum':
                $baseAmount = 6000;
                break;
        }

        // // Add annual fee if applicable
        // if ($includeAnnualFee == "true") {
        //     $planbaseAmt = $baseAmount
        //     $baseAmount += 500;
        // }

        // Calculate GST
        $cgstRate = 9;
        $sgstRate = 9;
        $cgstAmount = ($baseAmount * $cgstRate) / 100;
        $sgstAmount = ($baseAmount * $sgstRate) / 100;
        $totalTax = $cgstAmount + $sgstAmount;
        $totalAmount = $baseAmount + $totalTax;
        
        $AnnualTotalTax = $totalTax;
        $AnnualTotalAmount = $totalAmount;
        $annualFeeApplied = 0;
        

        if ($includeAnnualFee == "true") {
            $annualFeeApplied = 500;
            $AnnualTotalAmount = $totalAmount + 590;
            $AnnualTotalTax = $totalTax + 90;
        }
        


        // Generate invoice number
        // $invoiceNumber = 'AQ/' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT) . '/' . date('y') . '-' . (date('y') + 1);
        // $invoiceNumber = 'AQ-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT) . '-' . date('y') . '-' . (date('y') + 1);
        // 

        // Fetch the InvoiceCounter object
        // $notification = ProNotification::getByPath("/Services/Notifications/$notificationId");
        $counter = InvoiceCounter::getByPath("/Counters/OnlineInvoiceCounter");
        

        if (!$counter) {
            return $this->json(['error' => 'InvoiceCounter not found'], 500);
        }

        $lastNumber = $counter->getLastNumber();
        $newNumber = $lastNumber + 1;
        $counter->setLastNumber($newNumber);
        $counter->save();

        // Format: AQ/OI/25-26/ followed by padded number
        $invoiceNumber = 'AQ/OI/25-26/' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        // 
        $invoiceDate = date('d-M-y');

        // Prepare HTML for the invoice
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body { font-size: 12px; line-height: 1.5; }
                .invoice-header { margin-bottom: 20px; }
                .company-address { margin-bottom: 15px; }
                .invoice-details { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                table, th, td { border: 1px solid #ddd; }
                th, td { padding: 8px; text-align: left; }
                .text-right { text-align: right; }
                .signature { margin-top: 50px; }
                .footer { margin-top: 30px; font-size: 10px; }
                body {
                    font-family: DejaVu Sans, sans-serif;
                }
            </style>
        </head>
        <body>
            <div class="invoice-header">
                <center>
                    <img style="max-width: 200px;"src="https://arqonz.com/static/images/Logo-Arq.png" alt="">
                </center>
                <h2 style="text-align: center;">Tax Invoice</h2>
            </div>
            
            
            <div class="invoice-details">
                <table>
                    <tr>
                        <td width="50%">Invoice No.<br><strong>' . $invoiceNumber . '</strong></td>
                        <td width="50%">Dated<br><strong>' . $invoiceDate . '</strong></td>
                    </tr>
                    
                </table>
            </div>
            
            <div class="buyer-details">
                <table>
                    <tr>
                        <td width="50%">
                            <strong>ARQONZ GLOBAL PVT LTD</strong><br>
                            IIT Madras Research Park<br>
                            3rd floor, D-Block, Kanagam<br>
                            Taramani, Chennai<br>
                            GSTIN/UIN: 33AATCA8023B1ZX<br>
                            State Name : Tamil Nadu - 600113<br>
                        </td>
                        <td width="50%">
                            <strong>Buyer (Bill to)</strong><br>
                            ' . $user->getfirstname() . ' ' . $user->getlastname() . '<br>
                            ' . $user->getPortfolio()[0]->getStreetAddress() .','. $user->getPortfolio()[0]->getCity() .','. $user->getPortfolio()[0]->getState() .' - '.$user->getPortfolio()[0]->getPinCode() . '<br>
                            GSTIN/UIN: ' . ($user->getPortfolio()[0]->getgstnumber() ?: 'N/A') . '<br>
                            State Name: Tamil Nadu, Code: 33
                        </td>
                    </tr>
                    
                </table>
            </div>
            
            <div class="items-table">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">Sl No.</th>
                            <th width="45%">Description of Services</th>
                            <th width="10%">HSN/SAC</th>
                            <th width="10%">GST Rate</th>
                            
                            <th width="10%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>' . $plan . ' Plan Subscription Fee</td>
                            <td>9983</td>
                            <td>18%</td>
                            
                            <td class="text-right">₹' . number_format($baseAmount, 2) . '</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>GST For Subscription Fee</td>
                            <td>9983</td>
                            <td></td>
                            
                            <td class="text-right">₹'.$totalTax.'</td>
                        </tr>
                        
                        ';

        if ($includeAnnualFee == "true") {
            $html .= '<tr>
                        <td>2</td>
                        <td>Annual Registration Fee</td>
                        <td>9983</td>
                        <td>18%</td>
                        
                        <td class="text-right">₹500.00</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>GST For Annual Registration Fee</td>
                        <td>9983</td>
                        <td></td>
                        
                        <td class="text-right">₹90.00</td>
                    </tr>
                    
                    ';
        }

        $html .= '  </tbody>
                    <tfoot>
                        <tr>
                            <td width="70%" colspan="6"><b>Total</b></td>
                            <td width="30%" colspan="1">₹' . number_format($AnnualTotalAmount, 2) . '</td>
                        </tr>
                    </tfoot>
                    
                </table>
            </div>
            
            <div class="total-section">
                <table>
                    <tr>
                        <td width="60%">Amount Chargeable (in words)<br> E. & O.E</td>
                        <td width="40%">INR ' . $this->numberToWords($AnnualTotalAmount) . ' Only</td>
                    </tr>
                </table>
                

                <table>
                    <tr>
                        <td width="60%"><b>Declaration</b><br>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</td>
                        <td width="40%" class="text-right">for ARQONZ GLOBAL PVT LTD<br><img style="max-width: 50px;"src="https://arqonz.com/static/images/MySignature.png" alt=""><br>Authorised Signatory</td>
                    </tr>
                </table>
                <p style="text-align: center;">This is a Computer Generated Invoice</p>
            </div>
            
            
        </body>
        </html>';

        // Set up DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'portrait');
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $html;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save PDF to Pimcore assets
        $pdfOutput = $dompdf->output();
        $asset = new Asset();
        $safeInvoiceNumber = str_replace('/', '-', $invoiceNumber);
        $asset->setFilename('invoice_' . $safeInvoiceNumber . '.pdf');
        $asset->setData($pdfOutput);
        $asset->setParent(Asset::getByPath("/invoices"));
        $asset->save();

        return $this->json([
            'success' => true,
            'invoicePath' => $asset->getFullPath(),
            'invoiceNumber' => $invoiceNumber
        ]);
    }

    // Helper function to convert numbers to words (add this to your controller)
    private function numberToWords($number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        
        $rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? " and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($rupees ? $rupees . 'Rupees ' : '') . $paise;
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

        $customer = $ProProfile -> getCustomer();

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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $$EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
    
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



    /**
     * @Route("/account/BOQ/{url}", name="Account-Requirement-Details")
     */
    public function AccountRequirementDetailsAction($url, Security $security, Request $request, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {

            // Load ArchitectProfile based on the URL
            $ProRequirement = ProRequirement::getByPath("/Requirements/$url");

            // Update page views count
            $currentViews = $ProRequirement->getPageViewsCount();
            if (empty($currentViews)) {
                $ProRequirement->setPageViewsCount(1); // Initialize with 1 if empty or null
            } else {
                $ProRequirement->setPageViewsCount($currentViews + 1); // Increment by 1
            }
            $ProRequirement->save();


            if (!$ProRequirement) {
                throw $this->createNotFoundException('Project not found');
            }

            
            $ProProfile = $ProRequirement->getProfessional();
            $customer = $user;
            $UserProfile = $customer->getPortfolio()[0];
            // $customer = $ProProfile -> getCustomer();

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

                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                
                $htmlContent = str_replace("[Customer Name]", $form->get('fullname')->getData(), $htmlContent);
                $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($customer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();
        
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

            // Filter Relevant Products
            $customerProducts = $UserProfile->getProducts();
            $customerTags = [];

            foreach ($customerProducts as $product) {
                $tags = $product->getTags(); // Assuming 'Tags' field is a comma-separated string
                $customerTags = array_merge($customerTags, array_map('trim', explode(',', $tags)));
            }


            $enabledProducts = [];
            $disabledProducts = [];

            
                

            foreach ($ProRequirementProducts as $product) {
                $enabled = false;
                $productNameCleaned = strtolower(preg_replace('/\s+/', ' ', trim($product->getProductName())));
                if (in_array($productNameCleaned, array_map('strtolower', $customerTags), true)) {
                    $enabled = true;
                }
                if ($enabled) {
                    $enabledProducts[] = $product;
                } else {
                    $disabledProducts[] = $product;
                }
            }

            

            return $this->render('Professional/Professional_BOQ_single.html.twig', [
                'architectProfile' => $ProProfile,
                'UserProfile' => $UserProfile,
                'ProProject' => $ProRequirement,
                'ProRequirementProducts' => $ProRequirementProducts,
                'form' => $form->createView(),
                'form1' => $form1->createView(),
                'form2' => $form2->createView(),
                'customer' => $customer,
                'enabledProducts' => $enabledProducts,
                'disabledProducts' => $disabledProducts,
            ]);
        }

        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    
            
    }


    /**
     * @Route("/emailVerification/{url}", name="Email-Verification")
     */
    public function EmailVerificationAction($url, Request $request, Security $security, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $UserverificationToken = $customer->getEmailVerificationToken();
            if ($UserverificationToken ===  $url) {
                $Verificationstatus = 'success';
                $customer->setEmailVerified('true');
                $customer->save();
            }
            else {
                $Verificationstatus = 'fail';
            }
            
            return $this->render('Professional/Email_verification_template.html.twig', [
                'status' =>  $Verificationstatus,
            ]);
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
     * @Route("/emailVerifications/resend", name="Email-Verification-resend", methods={"POST"})
     */
    public function EmailVerificationResendAction(Security $security, MailerInterface $mailer)
    {
        $user = $security->getUser();
        
        if (!$user || !$this->isGranted('ROLE_USER')) {
            // Return an error response if user is not authenticated or doesn't have a role
            return new JsonResponse(['success' => false, 'message' => 'User not authenticated or insufficient role.']);
        }

        if ($user->getEmailVerified() === 'true') {
            // Return a failure response if the email is already verified
            return new JsonResponse(['success' => false, 'message' => 'Email is already verified.']);
        }

        // Assume getEmailVerificationToken() exists and retrieves a valid token
        $emailTokenURL = $user->getEmailVerificationToken();
        if (!$emailTokenURL) {
            return new JsonResponse(['success' => false, 'message' => 'No email verification token found.']);
        }

        // Load the email template
        $emailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
        $emailTemplates->addConditionParam("TemplateName = ?", "EmailVerificationEmail");
        $emailTemplate = $emailTemplates->load()[0];

        // Replace placeholders in the email content
        $htmlContent = str_replace("{EmailTokenURL}", $emailTokenURL, $emailTemplate->getContent());
        $htmlContent = str_replace("{customerFirstName}", $user->getFirstName(), $htmlContent);

        // Send the email
        $mail = new \Pimcore\Mail();
        $mail->to($user->getEmail());
        $mail->subject($emailTemplate->getSubject());
        $mail->html($htmlContent);
        $mail->send();

        return new JsonResponse(['success' => true, 'message' => 'Verification email resent successfully.']);
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

                    // $ProProduct->setInternationalBrand($form->get('InternationalBrand')->getData() ?? false);
                    $internationalBrandValue = false;
                    if ($form->has('InternationalBrand')) {
                        $internationalBrandData = $form->get('InternationalBrand')->getData();
                        $internationalBrandValue = ($internationalBrandData === 'on' || $internationalBrandData === true);
                    }
                    $ProProduct->setInternationalBrand($internationalBrandValue);


                    $ProProduct->setParentCategory($form->get('ParentCategory')->getData());
                    $ProProduct->setSubCategory($form->get('SubCategory')->getData());
                    $ProProduct->setSubSubCategory($form->get('SubSubCategory')->getData());
                    // $ProProduct->setProductCategories($form->get('categories')->getData());

                    // Handle image gallery upload (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    if ($galleryData) {
                        $items = [];
                        $videoPaths = [];
                        
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                        
                                if ($customertype === 'Manufacturer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                                
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                        
                        // Set the gallery
                        if (!empty($items)) {
                            $ProProduct->setProductImage(new ImageGallery($items));
                        }
                        
                        // Handle video paths (if any videos were uploaded)
                        if (!empty($videoPaths)) {
                            // Store video paths in the dedicated ProductVideoPaths field
                            $ProProduct->setProductVideoPaths(implode('|', $videoPaths));
                        }
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
    
                    // Redirect to the same route to clear the form
                    return $this->redirectToRoute('Manufacturer-add-product');
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => false,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/dealers/add-product", name="Dealers-add-product")
    */
    public function DealerProductSubmitAction(Request $request, Security $security, Translator $translator)
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
            if ($customertype === 'Dealer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct = new ProProduct();
                    $ProProduct->setParent(Service::createFolderByPath('/Services/Dealers/Products'));
        
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

                    // Handle image gallery upload (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    if ($galleryData) {
                        $items = [];
                        $videoPaths = [];
                        
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                        
                                if ($customertype === 'Manufacturer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                                
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                        
                        // Set the gallery
                        if (!empty($items)) {
                            $ProProduct->setProductImage(new ImageGallery($items));
                        }
                        
                        // Handle video paths (if any videos were uploaded)
                        if (!empty($videoPaths)) {
                            // Store video paths in the dedicated ProductVideoPaths field
                            $ProProduct->setProductVideoPaths(implode('|', $videoPaths));
                        }
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => false,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/supplier/add-product", name="supplier-add-product")
    */
    public function SupplierProductSubmitAction(Request $request, Security $security, Translator $translator)
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
            if ($customertype === 'Supplier' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct = new ProProduct();
                    $ProProduct->setParent(Service::createFolderByPath('/Services/Suppliers/Products'));
        
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

                    // Handle image gallery upload (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    if ($galleryData) {
                        $items = [];
                        $videoPaths = [];
                        
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                                
                                if ($customertype === 'Manufacturer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                        
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                        
                        // Set the gallery
                        if (!empty($items)) {
                            $ProProduct->setProductImage(new ImageGallery($items));
                        }
                        
                        // Handle video paths (if any videos were uploaded)
                        if (!empty($videoPaths)) {
                            // Store video paths in the dedicated ProductVideoPaths field
                            $ProProduct->setProductVideoPaths(implode('|', $videoPaths));
                        }
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
                    return $this->redirectToRoute('account-Products-list');
                    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => false,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }

    /**
    * @Route("/retailer/add-product", name="retailer-add-product")
    */
    public function RetailerProductSubmitAction(Request $request, Security $security, Translator $translator)
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
            if ($customertype === 'Retailer' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct = new ProProduct();
                    $ProProduct->setParent(Service::createFolderByPath('/Services/Retailers/Products'));
        
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

                    // Handle image gallery upload (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    if ($galleryData) {
                        $items = [];
                        $videoPaths = [];
                        
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                                
                                if ($customertype === 'Manufacturer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                        
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                        
                        // Set the gallery
                        if (!empty($items)) {
                            $ProProduct->setProductImage(new ImageGallery($items));
                        }
                        
                        // Handle video paths (if any videos were uploaded)
                        if (!empty($videoPaths)) {
                            // Store video paths in the dedicated ProductVideoPaths field
                            $ProProduct->setProductVideoPaths(implode('|', $videoPaths));
                        }
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
                    return $this->redirectToRoute('account-Products-list');
                    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => false,
                ]);
            }
        }
    
        // If user is not an architect or architect is not activated
        return $this->render('Professional/NotLogged_signup.html.twig');
    }


    /**
    * @Route("/distributor/add-product", name="Distributor-add-product")
    */
    public function DistributorProductSubmitAction(Request $request, Security $security, Translator $translator)
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
            if ($customertype === 'Distributor' && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct = new ProProduct();
                    $ProProduct->setParent(Service::createFolderByPath('/Services/Distributors/Products'));
        
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

                    // Handle image gallery upload (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    if ($galleryData) {
                        $items = [];
                        $videoPaths = [];
                        
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                        
                                if ($customertype === 'Manufacturer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                        
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                        
                        // Set the gallery
                        if (!empty($items)) {
                            $ProProduct->setProductImage(new ImageGallery($items));
                        }
                        
                        // Handle video paths (if any videos were uploaded)
                        if (!empty($videoPaths)) {
                            // Store video paths in the dedicated ProductVideoPaths field
                            $ProProduct->setProductVideoPaths(implode('|', $videoPaths));
                        }
                    }  
                    $ProProduct->setPublished(true);    
                    $ProProduct->save();
    
                    $this->addFlash('success', $translator->trans('Product submitted succesfully.'));
                    return $this->redirectToRoute('account-Products-list');
                    
                    
                }

                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => false,
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
    public function DashboardProductEditAction($url, Request $request, Security $security, Translator $translator, LoggerInterface $logger)
    {
        $user = $security->getUser();

        $logger->info("This is URL: $url");
        
        
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $logger->info("This is ProProfile: $ProProfile");

            if ($customertype == "Professional") {
                $ProProfileType = $ProProfile->getProfessionalSupplierType();
            }
            else if ($customertype == "Supplier") {
                $ProProfileType = $ProProfile->getProfessionalSupplierType();
            }
            else {
                $ProProfileType = $customertype;
            }

            $logger->info("This is Customertype: $ProProfileType");
            
            
            

            $ProProduct = ProProduct::getByPath("/Services/". ucfirst($ProProfileType)."s/Products/$url");

            // If not found, try the Suppliers fallback location
            if (!$ProProduct) {
                $ProProduct = ProProduct::getByPath("/Services/Suppliers/Products/$url");
                $logger->info("Product not found in primary location, trying Suppliers fallback");
            }

            $logger->info("This is ProProduct: $ProProduct");

            


            $form = $this->createForm(AddProductFormType::class);
            
            // Populate form fields (same approach as projects)
            foreach ($form->all() as $formField) {
            $fieldName = $formField->getName();

                // Exclude ProductImage field (same as ProjectGallery exclusion)
                if ($fieldName !== 'ProductImage' && $fieldName !== '_submit') {
                    if ($fieldName === 'InternationalBrand') {
                        // Handle InternationalBrand field specially (same as ReraApproval in projects)
                        $internationalBrandValue = $ProProduct->getInternationalBrand();
                        $internationalBrandBoolean = $internationalBrandValue === '1' || $internationalBrandValue === true || $internationalBrandValue === 1;
                        $formField->setData($internationalBrandBoolean);
                    } else {
                        // Map form field names to class method names
                        $classFieldName = $fieldName;
                        if ($fieldName === 'ProductMaterial') {
                            $classFieldName = 'Material';
                        } elseif ($fieldName === 'ProductPrice') {
                            $classFieldName = 'Price';
                        } elseif ($fieldName === 'ProductUnit') {
                            $classFieldName = 'Unit';
                        } elseif ($fieldName === 'ProductTags') {
                            $classFieldName = 'Tags';
                        }
                        
                        $formField->setData($ProProduct->{'get' . ucfirst($classFieldName)}());
                    }
                }
            }
            
            $form->handleRequest($request);
            
            if (in_array($customertype, ['Manufacturer', 'Dealer', 'Distributor', 'Retailer', 'Supplier']) && $PortfolioActivate === 'true') {

                if ($form->isSubmitted() && $form->isValid()) {
                    $formData = $form->getData();
                    $ProProduct->setProductName($form->get('ProductName')->getData());
                    $ProProduct->setProductDescription($form->get('ProductDescription')->getData());
                    $ProProduct->setSpecifications($form->get('Specifications')->getData());
                    $ProProduct->setTags($form->get('ProductTags')->getData());
                    $ProProduct->setPrice($form->get('ProductPrice')->getData());
                    $ProProduct->setProductBrand($form->get('ProductBrand')->getData());
                    $ProProduct->setParentCategory($form->get('ParentCategory')->getData());
                    $ProProduct->setSubCategory($form->get('SubCategory')->getData());
                    $ProProduct->setSubSubCategory($form->get('SubSubCategory')->getData());

                    // Handle deleted images from request (same approach as projects)
                    $deletedImages = $request->request->get('deleted_images', '');
                    if (!empty($deletedImages)) {
                        if (is_array($deletedImages)) {
                            $deletedImageIds = $deletedImages;
                        } else {
                            $deletedImageIds = explode(',', $deletedImages);
                        }
                        $deletedImageIds = array_filter($deletedImageIds);
                        
                        foreach ($deletedImageIds as $imageId) {
                            if (!empty($imageId)) {
                                $image = Image::getById($imageId);
                                if ($image instanceof Image) {
                                    $image->delete();
                                }
                            }
                        }
                    } else {
                        $deletedImageIds = [];
                    }
                    
                    // Handle deleted videos from request
                    $deletedVideos = $request->request->get('deleted_videos', '');
                    if (!empty($deletedVideos)) {
                        if (is_array($deletedVideos)) {
                            $deletedVideoPaths = $deletedVideos;
                        } else {
                            $deletedVideoPaths = explode(',', $deletedVideos);
                        }
                        $deletedVideoPaths = array_filter($deletedVideoPaths);
                        
                        foreach ($deletedVideoPaths as $videoPath) {
                            if (!empty($videoPath)) {
                                // Delete the video file from the filesystem
                                $fullPath = '/var/www/pimcore/public' . $videoPath;
                                if (file_exists($fullPath)) {
                                    unlink($fullPath);
                                }
                            }
                        }
                    } else {
                        $deletedVideoPaths = [];
                    }
                    
                    // Handle new gallery uploads (same approach as projects)
                    $galleryData = $form->get('ProductImage')->getData();
                    $logger->info("Gallery data received: " . gettype($galleryData));
                    if ($galleryData) {
                        $logger->info("Gallery data is not empty");
                        if (is_array($galleryData)) {
                            $logger->info("Gallery data is array with " . count($galleryData) . " items");
                        }
                    } else {
                        $logger->info("Gallery data is empty");
                    }
                    $items = [];
                    $videoPaths = [];
                    
                    // Add existing images that weren't deleted
                    $gallery = $ProProduct->getProductImage();
                    if ($gallery instanceof ImageGallery) {
                        foreach ($gallery->getItems() as $item) {
                            if ($item instanceof Hotspotimage) {
                                $image = $item->getImage();
                                if ($image instanceof Image && !in_array($image->getId(), $deletedImageIds)) {
                                    $items[] = $item;
                                }
                            }
                        }
                    }
                    
                    // Add new uploaded images
                    if ($galleryData) {
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $file) {
                                if ($file) {
                                    $imageName = $file->getClientOriginalName();
                                    $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                                    
                                    // Check if it's a video file
                                    if (strpos($file->getMimeType(), 'video/') === 0) {
                                        // Handle videos - save to static directory (same as project functions)
                                        $videoDir = '/var/www/pimcore/public/static/videos';
                                        if (!file_exists($videoDir)) {
                                            mkdir($videoDir, 0777, true);
                                        }
                                        
                                        $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                                        $videoPath = $videoDir . '/' . $videoName;
                                        move_uploaded_file($file->getPathname(), $videoPath);
                                        
                                        // Store relative path (we'll handle this separately)
                                        $videoPaths[] = '/static/videos/' . $videoName;
                                    } else {
                                        // Handle images - create Pimcore asset
                        $newAsset = new Image();
                                        $newAsset->setFilename($imageName);
                                        $newAsset->setData(file_get_contents($file->getPathname()));
                                        
                                        // Set parent based on customer type
                                        if ($customertype === 'Manufacturer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                        } elseif ($customertype === 'Dealer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                        } elseif ($customertype === 'Distributor') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                        } elseif ($customertype === 'Supplier') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                        } elseif ($customertype === 'Retailer') {
                                            $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                        }
                                        
                                        $newAsset->save();
                                        
                                        $hotspotImage = new Hotspotimage();
                                        $hotspotImage->setImage($newAsset);
                                        $items[] = $hotspotImage;
                                    }
                                }
                            }
                        } else {
                            // Handle single file upload
                            $imageName = $galleryData->getClientOriginalName();
                            $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '-' . rand(1000, 9999) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                            
                            if (strpos($galleryData->getMimeType(), 'video/') === 0) {
                                // Handle videos - save to static directory (same as project functions)
                                $videoDir = '/var/www/pimcore/public/static/videos';
                                if (!file_exists($videoDir)) {
                                    mkdir($videoDir, 0777, true);
                                }
                                
                                $videoName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $galleryData->getClientOriginalName());
                                $videoPath = $videoDir . '/' . $videoName;
                                move_uploaded_file($galleryData->getPathname(), $videoPath);
                                
                                // Store relative path
                                $videoPaths[] = '/static/videos/' . $videoName;
                            } else {
                                // Handle images - create Pimcore asset
                                $newAsset = new Image();
                        $newAsset->setFilename($imageName);
                                $newAsset->setData(file_get_contents($galleryData->getPathname()));
                                
                                if ($customertype === 'Manufacturer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Manufacturers/ProductGallery"));
                                } elseif ($customertype === 'Dealer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                                } elseif ($customertype === 'Distributor') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Distributors/ProductGallery"));
                                } elseif ($customertype === 'Supplier') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Suppliers/ProductGallery"));
                                } elseif ($customertype === 'Retailer') {
                                    $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Retailers/ProductGallery"));
                                }
                                
                        $newAsset->save();
                                
                                $hotspotImage = new Hotspotimage();
                                $hotspotImage->setImage($newAsset);
                                $items[] = $hotspotImage;
                            }
                        }
                    }
                    
                    // Set the updated gallery
                    if (!empty($items)) {
                        $ProProduct->setProductImage(new ImageGallery($items));
                    } else {
                        $ProProduct->setProductImage(null);
                    }
                    
                    // Handle video paths (preserve existing + add new ones - exclude deleted)
                    $existingVideoPaths = $ProProduct->getProductVideoPaths();
                    $allVideoPaths = [];
                    
                    // Add existing video paths (if any) - exclude deleted ones
                    if (!empty($existingVideoPaths)) {
                        $existingArray = explode('|', $existingVideoPaths);
                        foreach ($existingArray as $path) {
                            if (!empty($path) && !in_array($path, $deletedVideoPaths)) {
                                $allVideoPaths[] = $path;
                            }
                        }
                    }
                    
                    // Add new video paths
                    if (!empty($videoPaths)) {
                        foreach ($videoPaths as $newPath) {
                            $allVideoPaths[] = $newPath;
                        }
                    }
                    
                    // Set all video paths
                    if (!empty($allVideoPaths)) {
                        $ProProduct->setProductVideoPaths(implode('|', $allVideoPaths));
                    } else {
                        $ProProduct->setProductVideoPaths(null);
                    }
                       
                    $ProProduct->save();
                    $this->addFlash('success', $translator->trans('Product Updated succesfully.'));
                }

                
                // Get existing images for preview (same approach as projects)
                $existingImages = [];
                $existingVideos = [];
                
                $gallery = $ProProduct->getProductImage();
                if ($gallery instanceof ImageGallery) {
                    foreach ($gallery->getItems() as $item) {
                        if ($item instanceof Hotspotimage) {
                            $image = $item->getImage();
                            if ($image instanceof Image) {
                                $existingImages[] = [
                                    'id' => $image->getId(),
                                    'path' => $image->getFullPath(),
                                    'thumbnail' => $image->getThumbnail('product_listing')
                                ];
                            }
                        }
                    }
                }
                
                // Get existing videos
                $videoPaths = $ProProduct->getProductVideoPaths();
                if (!empty($videoPaths)) {
                    $videoArray = explode('|', $videoPaths);
                    foreach ($videoArray as $videoPath) {
                        if (!empty($videoPath)) {
                            $existingVideos[] = $videoPath;
                        }
                    }
                }
                
                return $this->render('Professional/professional_add_product.html.twig', [
                    'form' => $form->createView(),
                    'customertype' => $customertype,
                    'customer' => $customer,
                    'editMode' => true,
                    'product' => $ProProduct,
                    'existingImages' => $existingImages,
                    'existingVideos' => $existingVideos,
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
            $EndorseeFirstName = ucfirst($form->get('Name')->getData());
            $ProEndorsement->setEmail($form->get('Email')->getData());
            $ProEndorsement->setPhone($form->get('Phone')->getData());
            $ProEndorsement->setQ3($form->get('q3')->getData());
            //$ProEndorsement->setProfession($form->get('Profession')->getData());
            $q1Text = $q1Value === 'Very Good' ? 'Very Good' : ($q1Value === 'Good' ? 'Good' : ($q1Value === 'Average' ? 'Average' : 'Poor'));
            $q2Text = $q2Value === 'Yes' ? 'Yes' : ($q2Value === 'No' ? 'No' : 'Maybe');
            $q3Text = $q3Value = $form->get('q3')->getData();

            // Create the desired string
            $Answers = sprintf('How Would you rate the Service Provided by: %s' . PHP_EOL . 'Would you Recommend this professionals to Others?: %s' . PHP_EOL . 'How much Would you rate overall Experience with Ajith (Out of 5)?: %s' , $q1Text, $q2Text, $q3Text);
            
            $ProEndorsement->setAnswers($Answers);

            $ProEndorsement->setPublished(true);
    
            $ProEndorsement->save();

            $endorsementpoints = $customer->getEndorsements();
            $customer->setEndorsements($endorsementpoints + 1);
            $customer->save();

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EndorsementReceived");
            $EmailTemplates = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplates[0];
            
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

            // SEND Email Finish


            // New Email
            $ReqEmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $ReqEmailTemplates->addConditionParam("TemplateName = ?", "EndorsementSuccessEmail");
            $ReqEmailTemplate = $ReqEmailTemplates->load();
            $ReqEmailTemplate = $ReqEmailTemplate[0];

            $Reqsubject = $ReqEmailTemplate->getSubject();
            $ReqhtmlContent = $ReqEmailTemplate->getContent();
            eval("\$ReqhtmlContent = \"$ReqhtmlContent\";");
            // Create a new Pimcore\Mail instance
            $Reqmail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $Reqmail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $Reqmail->to($form->get('Email')->getData());
            $Reqmail->subject($Reqsubject);
            $Reqmail->html($ReqhtmlContent);
            $Reqmail->send();
            
            $this->addFlash('success', 'Endorsement submitted succesfully.');
        }

        return $this->render('Professional/professional_Endorsement.html.twig', [
            'form' => $form->createView(),
            'name' => $capitalizedFirstName,
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/calculator/tile-calculator", name="Tile_Calculator")
     */
    public function TileCalculatorAction( Request $request, PaginatorInterface $paginator)
    {   

        $slug = 'tiles';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }        

        return $this->render('Professional/Calculators/tile_calculator.html.twig', [
            'products' => $products,
        ]);
    }






    /**
     * @Route("/calculator/wallpaper-calculator", name="WallPaper_Calculator")
     */
    public function WallPaperCalculatorAction( Request $request, PaginatorInterface $paginator)
    {   
        $slug = 'wooden-products';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }

        return $this->render('Professional/Calculators/Wallpaper_calculator.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/calculator/paint-calculator", name="Paint_Calculator")
     */
    public function PaintCalculatorAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/Paint_calculator.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/calculator/brick-calculator", name="Brick_Calculator")
     */
    public function BrickCalculatorAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/brick_calculator.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/calculator/wall-plaster-calculator", name="Wall_plaster_Calculator")
     */
    public function WallPlasterCalculatorAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/wall_plaster_calculator.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/calculator/concrete-calculator", name="Concrete_Calculator")
     */
    public function ConcreteCalculatorAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/concrete_calculator.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/calculator/length-converter", name="Length_Converter")
     */
    public function lengthConverterAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/length_converter.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/calculator/area-converter", name="Area_Converter")
     */
    public function AreaConverterAction( Request $request, PaginatorInterface $paginator)
    {
        $slug = 'bricks-&-blocks';
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
        $username = 'pimcoreuser';
        $password = 'G0H0me@T0day';
        $pdo = new \PDO($dsn, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Fetch product IDs and names
        $sql = "SELECT Category_Name FROM Product_Categories WHERE Category_Slug = :category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':category_slug', $slug);
        $stmt->execute();
        $categoryData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($categoryData) {  // Checks if $categoryData is not false
            $category = $categoryData['Category_Name'];  // Access the 'Category_Name' key directly
        } else {
            // Handle the case where no category was found
            $category = null;
            // Optionally, you can set a default category or handle this case differently
        }

        // Fetch product IDs and names
        $ProdSql = "SELECT ID, Unique_ID, Product_Name FROM products WHERE Product_Category = :Product_Category";
        $prodstmt = $pdo->prepare($ProdSql);
        $prodstmt->bindValue(':Product_Category', $category);
        $prodstmt->execute();
        $products = $prodstmt->fetchAll(\PDO::FETCH_ASSOC);
        $products = array_slice($products, 0, 12);


        // Fetch product prices
        foreach ($products as &$product) {
            // Logging the entire product array to understand its structure
            // $logger->info('Product Array:', ['product' => $product]);

            $productId = $product['ID'];
            $uniqueId = $product['Unique_ID'];
            // $logger->info("Product ID: $productId");

            $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
            $priceStmt = $pdo->prepare($priceSql);
            $priceStmt->bindValue(':productId', $productId);
            $priceStmt->execute();
            $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

            $product['Product_Price'] = $priceData ? $priceData['Product_Price'] : 0.0;
            $product['Product_Unit'] = $priceData ? $priceData['Product_Unit'] : 0.0;
            // $logger->info("Product Price: " . $product['Product_Price']);


            $imageSql = "SELECT product_image_Path FROM product_images WHERE Unique_ID = :productId";
            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':productId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($imageDatas) > 0) {
                $imageData = $imageDatas[0];
                // Now you can safely use $imageData['product_image_Path']
            } else {
                // Handle the case where no image was found
                $imageData = null;
                // Optionally, you can set a default image or handle this case differently
            }

            if ($imageData) {
                // Replace backslashes with forward slashes
                $imagePath = str_replace('\\', '/', $imageData['product_image_Path']);
                $product['Image_Path'] = $imagePath;
                // $logger->info("Product Image Path: " . $product['Image_Path']);
            } else {
                $product['Image_Path'] = null;
            }           
            
        }
        
        return $this->render('Professional/Calculators/area_converter.html.twig', [
            'products' => $products,
        ]);
    }





   /**
     * @Route("/chatgpt", name="chatgpt")
     */
    public function ChatGptAction(Request $request, LoggerInterface $logger, SessionInterface $session)
    {
        // Log start of request
        $logger->info('ChatGptAction called.');

        // Retrieve conversation history and thread ID from session
        // $conversationHistory = $session->get('conversationHistory', []);
        $conversationHistory = null;
        $threadId = null;

        // Log session data
        $logger->info('Session threadId: ' . json_encode($threadId));
        $logger->info('Conversation History: ' . json_encode($conversationHistory));

        if ($request->isMethod('POST')) {
            $userMessage = $request->request->get('message');

            // Log the received user message
            $logger->info('User Message: ' . $userMessage);

            // If thread ID is not set, create a new thread
            if (!$threadId) {
                $logger->info('Thread ID is not set. Creating a new thread...');
                $threadId = $this->createThread($logger);
                if (!$threadId) {
                    $logger->error('Failed to create a new thread.');
                    $this->addFlash('error', 'Could not create a new thread.');
                    return $this->redirectToRoute('chatgpt');
                }
                $session->set('threadId', $threadId);
                $logger->info('New Thread ID created: ' . $threadId);
            }

            // Add user's message to the thread
            $messageId = $this->addMessageToThread($threadId, $userMessage, $logger);
            if (!$messageId) {
                $logger->error('Failed to add message to thread.');
                $this->addFlash('error', 'Could not add message to thread.');
                return $this->redirectToRoute('chatgpt');
            }
            $logger->info('Message added to thread. Message ID: ' . $messageId);

            // Run the thread and get assistant's response
            $assistantResponse = $this->runThread($threadId, $logger);
            if (!$assistantResponse) {
                $assistantResponse = 'Sorry, I could not get a response from the assistant.';
                $logger->error('Failed to get a response from assistant.');
            } else {
                $logger->info('Assistant Response: ' . $assistantResponse);
            }

            // Update conversation history
            $conversationHistory[] = [
                'user' => $userMessage,
                'bot' => $assistantResponse,
            ];

            // Save conversation history back to session
            $session->set('conversationHistory', $conversationHistory);
            $logger->info('Updated conversation history saved to session.');
        }

        return $this->render('/Professional/Chat/chat_interface.html.twig', [
            'conversationHistory' => $conversationHistory,
        ]);
    }

    private function createThread(LoggerInterface $logger)
    {
        $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
        $apiKey = $openaiConfig['api_key'];
        $apiUrl = 'https://api.openai.com/v1/threads';
        $client = new Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                    'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                ],
                'json' => new \stdClass(), // Empty JSON body
            ]);

            $responseData = json_decode($response->getBody(), true);
            // $logger->info('Thread creation response: ' . json_encode($responseData));

            return $responseData['id'] ?? null;
        } catch (\Exception $e) {
            $logger->error('Error creating thread: ' . $e->getMessage());
            return null;
        }
    }

    private function addMessageToThread($threadId, $userMessage, LoggerInterface $logger)
    {
        $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
        $apiKey = $openaiConfig['api_key'];
        $apiUrl = 'https://api.openai.com/v1/threads/' . $threadId . '/messages';
        $client = new Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                    'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                ],
                'json' => [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $userMessage,
                        ]
                    ],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);
            $logger->info('Message added response: ' . json_encode($responseData));

            return $responseData['id'] ?? null;
        } catch (\Exception $e) {
            $logger->error('Error adding message to thread: ' . $e->getMessage());
            return null;
        }
    }

    private function runThread($threadId, LoggerInterface $logger)
    {
        // Path to your Python script
        $pythonScriptPath = '/var/www/pimcore/public/static/files/python/runThread.py';

        // Execute the Python script and pass the threadId
        try {
            // $command = 'PATH=/usr/local/bin:/usr/bin:/bin python3 ' . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($threadId);

            $process = proc_open('/usr/bin/python3 ' . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($threadId), [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ], $pipes);
            
            $output = stream_get_contents($pipes[1]);
            // $error = stream_get_contents($pipes[2]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            
            // $logger->info('Python output: ' . $output);
            // $logger->error('Python error: ' . $error);

            // $logger->info('Python script output: ' . $output);

            if (!$output) {
                $logger->error('No output from Python script.');
                return null;
            }

            if ($output) {
                // $logger->info('Completed message from Python script: ' . $output);
                return $output;
            } else {
                $logger->error('No output from Python script.');
                return null;
            }
        } catch (\Exception $e) {
            $logger->error('Error running Python script: ' . $e->getMessage());
            return null;
        }
    }

    // AQIQ Original API Starts here

    /**
     * @Route("/arqonz-chat", name="Arqonz-Chat")
     */
    public function ArqonzChat(Request $request, Security $security, LoggerInterface $logger, SessionInterface $session)
    {   
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $subscriptionStart = $customer->getSubscriptionStart();

            if ($subscriptionStart) {
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $oneYearAfterSubscription = clone $subscriptionStart;
                $oneYearAfterSubscription->modify('+1 year');
                
                // If current date is after one year of subscription start, show annual fee
                if ($now >= $oneYearAfterSubscription) {
                    return $this->redirect('/account/pricing');
                }
            } else {
                // First time user, show annual fee
                return $this->redirect('/account/pricing');
            }

            $AqThreads = $customer->getAqThread();
            // Reverse the order of elements in the $AqThreads array
            $AqThreads = array_reverse($AqThreads);



            return $this->render('/Professional/Chat/initial_chat_interface.html.twig', [
                'AqThreads' => $AqThreads,
            ]);
        }
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/create-thread", name="create_thread", methods={"POST"})
     */
    public function createThreadAPI(LoggerInterface $logger, Security $security): JsonResponse
    {   
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $openAIConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
            $apiKey = $openAIConfig['api_key'];
            $apiUrl = $openAIConfig['api_url'];
            
            if (empty($apiKey)) {
                throw new \Exception('OpenAI API key not configured');
            }
            $client = new \GuzzleHttp\Client();

            try {
                $response = $client->post($apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                        'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                    ],
                    'json' => new \stdClass(), // Empty JSON body
                ]);

                $responseData = json_decode($response->getBody(), true);
                $threadId = $responseData['id'] ?? null;

                if ($threadId) {
                    // Return a JSON response with the thread ID


                    return new JsonResponse([
                        'success' => true,
                        'threadId' => $threadId
                    ]);
                } else {
                    // Handle the case where the thread creation failed
                    return new JsonResponse(['success' => false, 'message' => 'Thread creation failed'], 500);
                }
            } catch (\Exception $e) {
                // Log the error and return a failure response
                $logger->error('Error creating thread: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Server error'], 500);
            }
        } 
        // If user is not an architect or architect is not activated
        return new JsonResponse(['success' => false, 'message' => 'Invalid Authentication'], 500);
    }


    /**
     * @Route("/arqonz-chat/{threadid}", name="Arqonz-Chat-Thread")
     */
    public function ArqonzChatThread($threadid, Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {
        
        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {

            $customer = $user;
            $AqThreads = $customer->getAqThread();
            // Reverse the order of elements in the $AqThreads array
            $AqThreads = array_reverse($AqThreads);

            $threadId = $threadid;

            $AQThreadsList = new \Pimcore\Model\DataObject\AqThread\Listing();
            $AQThreadsList->addConditionParam("ThreadId = ?", $threadId);
            $AQThreads = $AQThreadsList->load();
            
            
            
            if (empty($AQThreads)) {
                $conversationHistory = null;
            }
            else {
                $AQThread = $AQThreads[0];
                $conversationHistoryJson = $AQThread->getConversationHistory();
                $conversationHistory = json_decode($conversationHistoryJson, true); 
            }


            return $this->render('/Professional/Chat/thread_interface.html.twig', [
                'threadId' => $threadId,
                'conversationHistory' => $conversationHistory,
                'AqThreads' => $AqThreads,
            ]);
        } 
        // If the user is not an architect or the architect is not activated
        return $this->render('Architect/NotLogged_signup.html.twig');
    }



    /**
     * @Route("/arqonz-ai-send", name="arqonz_ai_send", methods={"POST"})
     */
    public function sendMessage(Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {   
        // Retrieve the submitted form data

        $user = $security->getUser();
    
        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;
            $userMessage = $request->request->get('message');
            $threadId = $request->request->get('threadId');
            $logger->info('threadId: ' . $threadId);
            $logger->info('userMessage: ' . $userMessage);

            $AQThreadsList = new \Pimcore\Model\DataObject\AqThread\Listing();
            $AQThreadsList->addConditionParam("ThreadId = ?", $threadId);
            $AQThreads = $AQThreadsList->load();
            
            
            if (empty($AQThreads)) {
                $AQThread = new AqThread();
                $AQThread->setParent(Service::createFolderByPath('/AQIQ-Threads'));
                $AQThread->setThreadId($threadId);
                $AQThread->setcustomer($customer);
                $AQThread->setConversationHistory('[]');
                $AQThread->setKey($threadId);

                if (strlen($userMessage) > 20) {
                    $threadTitle = substr($userMessage, 0, 20) . '...';
                } else {
                    $threadTitle = $userMessage;
                }

                $AQThread->setThreadTitle($threadTitle);
                $AQThread->setPublished(true);
                $AQThread->save();
            }
            else {
                $AQThread = $AQThreads[0];
            }

            $conversationHistoryJson = $AQThread->getConversationHistory();

            $conversationHistory = json_decode($conversationHistoryJson, true);            


            try {
                $messageId = null;

                $logger->info('threadId: ' . $threadId);

                $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
                $apiKey = $openaiConfig['api_key'];  // Replace with your actual API key
                $apiUrl = 'https://api.openai.com/v1/threads/' . $threadId . '/messages';
                $client = new Client();

                try {
                    $response = $client->post($apiUrl, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                            'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                        ],
                        'json' => [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $userMessage,
                                ]
                            ],
                        ],
                    ]);

                    $responseData = json_decode($response->getBody(), true);

                    $logger->info('Message added response: ' . json_encode($responseData));
                    $messageId = $responseData['id'] ?? null;

                } catch (\Exception $e) {
                    $logger->error('Error adding message to thread: ' . $e->getMessage());
                    $messageId = null;
                }

                if (!$messageId) {
                    $logger->error('Failed to add message to thread.');
                    $this->addFlash('error', 'Could not add message to thread.');
                    return $this->redirectToRoute('chatgpt');
                }
                $logger->info('Message added to thread. Message ID: ' . $messageId);

                $assistantResponse = $this->runThread($threadId, $logger);
                $assistantResponse = preg_replace('/【4:[^】]+】/u', '', $assistantResponse);
                if (!$assistantResponse) {
                    $assistantResponse = 'Sorry, I could not get a response from the assistant.';
                    $logger->error('Failed to get a response from assistant.');
                } else {
                    // $parsedown = new Parsedown();
                    // $assistantResponseHtml = $parsedown->text($assistantResponse);
                    $AqMessage = new AqMessage();
                    $AqMessage->setParent(Service::createFolderByPath('/AQIQ-Threads/Messages'));
                    $AqMessage->setMessageId($messageId);
                    $AqMessage->setAqThread($AQThread);
                    $AqMessage->setUserMessage($userMessage);
                    // $AqMessage->setBotReply($assistantResponseHtml);
                    $AqMessage->setBotReply($assistantResponse);
                    $AqMessage->setKey($messageId);
                    $AqMessage->setPublished(true);
                    $AqMessage->save();

                    $logger->info('Assistant Response: ' . $assistantResponse);
                }

                // Update conversation history
                $conversationHistory[] = [
                    'user' => $userMessage,
                    'bot' => $assistantResponse,
                ];

                $updatedConversationHistoryJson = json_encode($conversationHistory);
                $AQThread->setConversationHistory($updatedConversationHistoryJson);
                $AQThread->save();

                // Return the response
                return new JsonResponse(['success' => true, 'botResponse' => $assistantResponse]);
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
        }

        // If user is not an architect or architect is not activated
        return new JsonResponse(['success' => false, 'message' => 'Invalid Authentication'], 500);
    }



    /**
     * @Route("/vit/download-admin-card/{url}", name="Event-Admit-Card-download")
     */
    public function EventDownloadAdmitCard(Request $request, $url, Security $security, LoggerInterface $logger)

    { 
        $accountid = $url;


        // Fetch ProRequirement based on the key
        $customersList = new \Pimcore\Model\DataObject\Customer\Listing();
        $customersList->addConditionParam("UserID = ?", $accountid); 
        $customers = $customersList->load();
        if (!empty($customers)) {
            $customer = $customers[0];

            if ($customer->getEventPass()==='VIT') {
            
                // // Set up DOMPDF
                // $options = new Options();
                // $options->set('isHtml5ParserEnabled', true);
                // $options->set('isRemoteEnabled', true);
    
                // // Set the default paper size to A4 with no margins
                // $options->set('defaultPaperSize', 'A5');
                // $options->set('defaultPaperOrientation', 'portrait');
                // $options->set('isPhpEnabled', true);
    
                // $dompdf = new Dompdf($options);

                $snappy = new Pdf('/usr/bin/wkhtmltopdf');

                $pdfOptions = [
                    'page-size' => 'A5',              // Set the page size to A5
                    'orientation' => 'portrait',      // Set the orientation to portrait
                    'margin-top' => '0mm',            // Set margins if needed
                    'margin-right' => '0mm',
                    'margin-bottom' => '0mm',
                    'margin-left' => '0mm'
                ];
    
                // Prepare HTML content with watermark
                
                $html2 = <<<HTML
                <html>
                <head>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Mukta:wght@400;800&family=Oswald:wght@700&display=swap');
                    *{
                        margin: 0;
                        padding:0;
                        box-sizing: border-box;
                    }
                    body {
                        background-color: #FEFBF2;
                    }
                    .main {
                            min-height: 92%;
                            padding: 10px 20px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            border: 3px solid #000;
                            margin: 37px;
                        }
                    .CardHeadingcontainer {
                            display:flex;
                            justify-content: center;     
                        }
                    .cardheading {
                        text-transform: uppercase;
                        font-family: "Mukta", sans-serif;
                        font-weight: 800;
                        color: #393F49;
                        position: absolute;
                        top: 11px;
                        left: 37%;
                        background-color: #FEFBF2;
                        padding: 0px 18px;
                    }
                    .logos {
                        display:flex;
                        max-height: 100px;
                        justify-content: space-around;
                        align-items: center;
                    }
                    .ArqonzLogo, .VITLOGO {
                        max-height: 100px;
                    }
                    .Zemchlogo {
                        max-height: 50px;
                    }
                    .InviteTitle {
                        display: flex;
                        justify-content: center;
                        margin: 20px 0px;
                    }
                    .InviteTitle h2 {
                        font-family: "Oswald", sans-serif;
                        font-weight: 800;
                        font-size: 50px;
                    }
                    
                    .MeetingType {
                        display: flex;
                        justify-content: center;
                    }
                    .MeetingType h2 {
                        text-transform: uppercase;
                        font-family: "Mukta", sans-serif;
                        font-weight: 600;
                        font-size: 31px;
                        color: #393F49;
                    }
                    .timeelement {
                        display: flex;
                        gap:10px;
                        align-items: center;
                    }
                    .timeelement img {
                        max-width:35px;
                    }
                    .timeelement p {
                        font-family: "Mukta", sans-serif;
                        font-weight: 600;
                        font-size: 22px;
                        margin-left:10px;
                    }
                    .TimeLocation {
                        display: flex;
                        justify-content: space-around;
                        margin: 13px 0px;
                    }
                    .DateTime {
                        display: flex;
                        justify-content: center;
                    }
                    .AttendeeDetails {
                        display: flex;
                        justify-content: center;
                        flex-direction: column;
                        align-items: center;
                        margin: 39px 0px;
                        gap: 12px;
                    }
                    .AttendeeDetails h2 {
                        text-transform: uppercase;
                        color: #393F49;
                        margin: 15px 0px;
                    }


                    
                        
                    </style>
                </head>
                <body>
                    <div class="main">
                    <div class="CardHeadingcontainer">
                        <h1 class="cardheading">Admit Card</h1>
                    </div>

                        <div class="logos">
                        <img src="https://arqonz.com/static/images/VIT/Arqonz-logo-icon-vit.png" alt="" class="ArqonzLogo">
                        <img src="https://arqonz.com/static/images/VIT/Zemch-network.png" alt="" class="Zemchlogo">
                        <img src="https://arqonz.com/static/images/VIT/vitlogo.png" alt="" class="VITLOGO">
                        </div>

                        <div class="InviteTitle">
                            <h2>ZEMCH '24</h2>
                        </div>

                        <div class="MeetingType">
                            <h2>General Meeting</h2>
                        </div>
                            <div class="TimeDateLocation">
                            <div class="TimeLocation">
                                <div class="timeelement">
                                    <img src="https://arqonz.com/static/images/VIT/vit-calender.svg" alt="">
                                    <p>8th November, 2024</p>
                                </div>
                                <div class="timeelement">
                                    <img src="https://arqonz.com/static/images/VIT/vit-location-pin.svg" alt="">
                                    <p>VIT, Vellore</p>
                                </div>
                            </div>

                            <div class="DateTime">
                                <div class="timeelement">
                                    <img src="https://arqonz.com/static/images/VIT/vit-clock.svg" alt="">
                                    <p>2:00 - 5:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <div class="attendeedetailsCont">

                        <div class="AttendeeDetails">
                            <h2 class="AttendeeName">
                HTML;
                $html2 .= $customer->getfirstname();
                $html2 .= ' ';
                $html2 .= $customer->getlastname();
                        
                $html2 .= <<<HTML

                                    </h2>
                                    <h2 class="attendeeDesig">
                        HTML;

                $html2 .=  $customer->getcustomertype();

                if ($customer->getcompany() !== '') {
                    $html2 .= <<<HTML

                                    </h2>
                                    <h2 class="Company">
                        HTML;

                $html2 .=  $customer->getcompany();
                }

                if ($customer->getcity() !== '') {
                    $html2 .= <<<HTML

                                    </h2>
                                    <h2 class="City">
                        HTML;

                $html2 .=  $customer->getcity();
                }

                $html2 .= <<<HTML
                                    </h2>
                                    <!-- <h2 class="attendeeCompany">Super Architects Pvt. Ltd</h2>
                                    <h2 class="attendeecity">Chennai</h2> -->
                                    <h2 class="UID">UID: 
                        HTML;
                $html2 .=  $customer->getUserID();
                $html2 .= <<<HTML
                                    </h2>
                                </div>
                            </div>

                            </div>
                        </body>
                        </html>    
                    HTML;
    
                // $html .= '</tbody></table>';
    
                // $html2 .= '</body></html>';
    
                // // Load HTML content into DOMPDF
                // $dompdf->loadHtml($html2);
    
                // // (Optional) Setup the paper size and orientation
                // $dompdf->setPaper('A5', 'portrait');
    
                // // Render the HTML as PDF
                // $dompdf->render();
    
                // // Output the generated PDF to Browser
                // $pdfOutput = $dompdf->output();

                $pdfContent = $snappy->getOutputFromHtml($html2, $pdfOptions);
    
                // Prepare and send PDF as response
                // $response = new Response($pdfOutput);
                // $response->headers->set('Content-Type', 'application/pdf');
                // $response->headers->set('Content-Disposition', 'attachment; filename="ZEMCH-2024-Admit-Card.pdf"');
    
                // $logger->info('PDF generated successfully');
                
                // return $response;

                return new Response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="ZEMCH-2024-Admit-Card.pdf"',
                ]);
    
            }
        }


        

         // If the user is not an architect or the architect is not activated
         return $this->render('Architect/InvalidUserID.html.twig');
    }


    /**
     * @Route("/arqonz-zemch-registration", name="Arqonz-Zemch-register", methods={"POST"})
     */
    public function arqonzzemchregisterAction(Security $security, MailerInterface $mailer)
    {
        $user = $security->getUser();
        
        $customer = $user;
        
        if (!$user || !$this->isGranted('ROLE_USER')) {
            // Return an error response if user is not authenticated or doesn't have a role
            return new JsonResponse(['success' => false, 'message' => 'User not authenticated or insufficient role.']);
        }

        if ($user->getEventPass() === 'VIT') {
            // Return a failure response if the email is already verified
            return new JsonResponse(['success' => false, 'message' => 'User Already Registered']);
        }

        $customer->setEventPass('VIT');
        $customer->save();


        // Load the email template
        $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
        $EmailTemplates->addConditionParam("TemplateName = ?", "ZEMCHRegisterEmail");
        $EmailTemplate = $EmailTemplates->load();
        $EmailTemplate = $EmailTemplate[0];


        $subject = $EmailTemplate->getSubject();
        $htmlContent = $EmailTemplate->getContent();
        $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);
        $htmlContent = str_replace("{AdmitcardURL}", "https://arqonz.com/vit/download-admin-card/".$customer->getUserID(), $htmlContent);
        
        // Create a new Pimcore\Mail instance
        $mail = new \Pimcore\Mail();
        // $mail->from('arqonztest@gmail.com');
        $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
        $mail->to($customer->getEmail());
        $mail->subject($subject);
        $mail->html($htmlContent);
        $mail->send();

        $adminId="vit/download-admin-card/".$customer->getUserID();
        $WelcometemplateID= "d5c55d46-ea43-42f7-872e-899a4d6fc507";
        $this->sendZemchEventWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $adminId, $WelcometemplateID);

        $ZemchtemplateID = "b38afabc-a26c-4e86-8601-e2ab20113d2f";
        $this->GupsendWhatsAppMessage($customer->getPhone(), $customer->getFirstName(), $ZemchtemplateID);



        return new JsonResponse(['success' => true, 'message' => 'Registered Successfully']);
    }

    private function sendZemchEventWhatsAppMessage($phoneNumber, $firstName, $admitId, $templateID)
    {
        // Get Gupshup API credentials from environment
        $gupshupConfig = \App\Service\EnvironmentConfigService::getGupshupConfig();
        $apiUrl = $gupshupConfig['api_url'];
        $apiKey = $gupshupConfig['api_key'];
        $source = $gupshupConfig['source_number'];
        $srcName = $gupshupConfig['source_name'];
        
        if (empty($apiKey) || empty($source) || empty($srcName)) {
            throw new \Exception('Gupshup WhatsApp API credentials not configured');
        }
        $templateId = $templateID;  // Your template ID
        
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



    private function GupsendWhatsAppMessage($phoneNumber, $firstName, $templateID)
    {
        // Get Gupshup API credentials from environment
        $gupshupConfig = \App\Service\EnvironmentConfigService::getGupshupConfig();
        $apiUrl = $gupshupConfig['api_url'];
        $apiKey = $gupshupConfig['api_key'];
        $source = $gupshupConfig['source_number'];
        $srcName = $gupshupConfig['source_name'];
        
        if (empty($apiKey) || empty($source) || empty($srcName)) {
            throw new \Exception('Gupshup WhatsApp API credentials not configured');
        }
        $templateId = $templateID;  // Your template ID
        
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



    /**
     * @Route("/send-login-otp", name="Send-login-otp", methods={"POST"})
     */
    public function SendLoginOTP(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['username'] ?? null;
            

            

            $CustomersList = new \Pimcore\Model\DataObject\Customer\Listing();
            $CustomersList->addConditionParam("email = ?", $email);
            $Customers = $CustomersList->load();
            $Customer = $Customers[0];


            // Log the received data (optional)
            $logger->info('Received product enquiry', [
                'email' => $email,
                'customer' => $Customer,
            ]);

            $otp = random_int(100000, 999999);
            $Customer -> setOtp($otp);
            $Customer -> save();

            // $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
            // $this->GupsendWhatsAppMessage($Customer->getPhone(), $otp, $OTPtemplateID);

            // Format the WhatsApp message with OTP
            $whatsAppMessage = "*{$otp}* is your verification code. For your security, do not share this code.";
            $this->sendWhatsAppMessage($Customer->getPhone(), $whatsAppMessage);

            // Email
            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "LoginOTPEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();

            
            $htmlContent = str_replace("[customername]", $Customer->getfirstname(), $htmlContent);
            $htmlContent = str_replace("[OTP]", $otp, $htmlContent);
            
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($Customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();
           

            // Return success response
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $logger->error('Error Sending OTP: ' . $e->getMessage());

            // Return failure response
            return new JsonResponse(['success' => false], 500);
        }
    }



    private function sendWhatsAppMessage(string $phoneNumber, string $message): void
    {
        // Get WhatsApp API configuration from environment
        $whatsappConfig = \App\Service\EnvironmentConfigService::getWhatsAppConfig();
        $apiUrl = $whatsappConfig['api_url'];
        $apiKey = $whatsappConfig['api_key'];
        
        if (empty($apiKey) || empty($apiUrl)) {
            throw new \Exception('WhatsApp API credentials not configured');
        }
        
        // Format the phone number to include the @c.us suffix
        $chatId = $phoneNumber;
        if (!str_ends_with($phoneNumber, '@c.us')) {
            $chatId = '91'.$phoneNumber . '@c.us';
        }
        
        // Prepare the request data
        $requestData = [
            'chatId' => $chatId,
            'contentType' => 'string',
            'content' => $message
        ];
        
        // Initialize cURL
        $ch = curl_init($apiUrl);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey  // Changed from api_key to x-api-key
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        
        // Add these lines for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Get the verbose log
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        
        // Log both response and verbose output
        error_log('WhatsApp API Response: ' . $response);
        error_log('Verbose log: ' . $verboseLog);
        
        // Handle errors if needed
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            error_log('WhatsApp API Error: ' . $error_msg);
            throw new \Exception("WhatsApp API request failed: " . $error_msg);
        }
        
        // Close the cURL session
        curl_close($ch);
    }




    // THIS IS FOR THE MOBILE APP OTP VERIFICATION
    /**
     * @Route("/verify-otp", name="verify-otp", methods={"POST"})
     */
    public function verifyOTP(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['username'] ?? null;
            $otp = $data['otp'] ?? null;

            if (!$email || !$otp) {
                return new JsonResponse(['success' => false, 'message' => 'Missing email or OTP'], 400);
            }

            $CustomersList = new \Pimcore\Model\DataObject\Customer\Listing();
            $CustomersList->addConditionParam("email = ?", $email);
            $Customers = $CustomersList->load();

            if (empty($Customers)) {
                return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
            }

            $Customer = $Customers[0];

            if ((int) $Customer->getOtp() === (int) $otp) {
                // OTP verification success
                $logger->info("OTP Verified for User: " . $email);
                $customerType = $Customer->getcustomertype();
                $customerID = $Customer->getUserID();


                // Login the user
                $response = new JsonResponse(['success' => true, 'message' => 'Login successful', 'user' => [
                    'id' => $Customer->getKey(),
                    'email' => $Customer->getemail(),
                    'name' => $Customer->getfirstname(),
                    'customerType' => $customerType,
                    'customerID' => $customerID,
                    'subscriptionStart' => $Customer->getSubscriptionStart() ? $Customer->getSubscriptionStart()->format('Y-m-d') : null,



                ]]);

                // $loginManager->login($Customer, $request, $response);

                return $response;
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Invalid OTP'], 400);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }



    /**
     * @Route("/phone-verify-otp", name="Phone-verify-otp", methods={"POST"})
     */
    public function PhoneVerifyOTP(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['username'] ?? null;
            

            

            $CustomersList = new \Pimcore\Model\DataObject\Customer\Listing();
            $CustomersList->addConditionParam("email = ?", $email);
            $Customers = $CustomersList->load();
            $Customer = $Customers[0];


            // Log the received data (optional)
            $logger->info('Received product enquiry', [
                'email' => $email,
                'customer' => $Customer,
            ]);

            $otp = random_int(100000, 999999);
            $Customer -> setMobileVerificationOTP($otp);
            $Customer -> save();

            $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
            $this->GupsendWhatsAppMessage($Customer->getPhone(), $otp, $OTPtemplateID);

           

            // Return success response
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $logger->error('Error Sending OTP: ' . $e->getMessage());

            // Return failure response
            return new JsonResponse(['success' => false], 500);
        }
    }

    /**
     * @Route("/dashboard-resend-otp", name="Dashboard-resend-OTP")
     */
    public function DashboardResendOTP(Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {
        $user = $security->getUser();
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $email = $user->getemail();

            try {                
    
                $CustomersList = new \Pimcore\Model\DataObject\Customer\Listing();
                $CustomersList->addConditionParam("email = ?", $email);
                $Customers = $CustomersList->load();
                $Customer = $Customers[0];
    
    
                // Log the received data (optional)
                $logger->info('Received product enquiry', [
                    'email' => $email,
                    'customer' => $Customer,
                ]);
    
                $otp = random_int(100000, 999999);
                $Customer -> setMobileVerificationOTP($otp);
                $Customer -> save();
    
                $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                $this->GupsendWhatsAppMessage($Customer->getPhone(), $otp, $OTPtemplateID);
               
                // Return success response
                return new JsonResponse(['success' => true]);
            } catch (\Exception $e) {
                $logger->error('Error Sending OTP: ' . $e->getMessage());
    
                // Return failure response
                return new JsonResponse(['success' => false], 500);
            }

        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/api/professional-dashboard/{customerID}", name="professional-dashboard-api", methods={"GET"})
     */
    public function professionalDashboardApiAction($customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $logger->info("Fetching customer with UserID: {$customerID}");

            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer, true));
            $logger->info("Customer class: " . get_class($customer));

            // Check if portfolio exists
            $proProfiles = $customer->getPortfolio();
            $logger->info("Customer Portfolio: " . print_r($proProfiles, true));

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile, true));

            // Get the required data
            $portfolioType = $proProfile->getPortfolioType();
            $profileCompletion = $customer->getProfileCompletion();
            $requirementsCount = count($proProfile->getRequirements() ?? []);
            $endorsementsCount = count($proProfile->getEndorsements() ?? []);
            $enquiriesCount = count($proProfile->getEnquiries() ?? []);

            $logger->info("PortfolioType: {$portfolioType}, ProfileCompletion: {$profileCompletion}");
            $logger->info("Requirements: {$requirementsCount}, Endorsements: {$endorsementsCount}, Enquiries: {$enquiriesCount}");

            // Prepare the response
            $responseData = [
                'portfolioType' => $portfolioType,
                'profileCompletion' => $profileCompletion,
                'requirementsCount' => $requirementsCount,
                'endorsementsCount' => $endorsementsCount,
                'enquiriesCount' => $enquiriesCount,
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $responseData,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Dashboard API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional dashboard data.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/profiles", name="profiles-api", methods={"GET"})
     */
    public function profilesApiAction(Request $request, LoggerInterface $logger, PaginatorInterface $paginator): JsonResponse
    {
        try {
            $portfolioType = $request->query->get('portfolioType', 'Architect');
            $currentPage = $request->query->getInt('page', 1);
            $perPage = 10; // Number of items per page

            // Fetch ProProfile objects with conditions
            $proProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
            $proProfileList->addConditionParam("PortfolioType = ?", $portfolioType);

            // Apply pagination directly on the query
            $totalItems = $proProfileList->getTotalCount();
            $totalPages = ceil($totalItems / $perPage);

            $proProfileList->setLimit($perPage);
            $proProfileList->setOffset(($currentPage - 1) * $perPage);

            $proProfiles = $proProfileList->load();

            $profiles = [];
            foreach ($proProfiles as $profile) {
                $profileImage = $profile->getProfileImage();
                $imageUrl = $profileImage ? $profileImage->getFullPath() : null;

                $profiles[] = [
                    'id' => $profile->getId(),
                    'companyName' => $profile->getCompanyName(),
                    'description' => implode(' ', array_slice(explode(' ', $profile->getDescription()), 0, 5)) . '...',
                    'yearEstablished' => $profile->getYearEstablished(),
                    'projectsCount' => count($profile->getProjects()),
                    'profileImage' => $imageUrl,
                    'Key' => $profile->getKey(),
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $profiles,
                'pagination' => [
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Profiles API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch profiles.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/profile/{profileType}/{id}", name="profile-detail-api", methods={"GET"})
     */
    public function profileDetailApiAction($profileType, $id, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {

            
            $proProfile = ProProfile::getByPath("/Services/$profileType/Profiles/$id");

            if (!$proProfile) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Profile not found.',
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Get profile image
            $profileImage = $proProfile->getProfileImage();
            $imageUrl = $profileImage ? $profileImage->getFullPath() : null;
            
            // Get creation date
            $creationTimestamp = $proProfile->getCreationDate();
            $creationDate = new \DateTime();
            $creationDate->setTimestamp($creationTimestamp);
            $formattedCreationDate = $creationDate->format('M Y');
            
            // Get projects count
            $proProjectsList = new \Pimcore\Model\DataObject\ProProject\Listing();
            $proProjectsList->addConditionParam("ProfessionalPath = ?", $proProfile);
            $projects = $proProjectsList->load();
            $projectsCount = count($projects);
            
            // Prepare the data for JSON response
            $profileData = [
                'id' => $proProfile->getFullPath(),
                'companyName' => $proProfile->getCompanyName(),
                'description' => $proProfile->getDescription(),
                'yearEstablished' => $proProfile->getYearEstablished(),
                'projectsCount' => $projectsCount,
                'rating' => $proProfile->getProRating(),
                'priceForHour' => $proProfile->getPriceForHour(),
                'experience' => $proProfile->getExperience(),
                'city' => $proProfile->getCity(),
                'citiesServed' => $proProfile->getCitiesServed(),
                'skills' => $proProfile->getSkills(),
                'portfolioType' => $proProfile->getPortfolioType(),
                'creationDate' => $formattedCreationDate,
                'profileImage' => $imageUrl,
            ];
            
            return new JsonResponse([
                'success' => true,
                'data' => $profileData,
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $logger->error('Profile Detail API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch profile details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/profile/{profileType}/{id}/projects", name="profile-projects-api", methods={"GET"})
     */
    public function profileProjectsApiAction($profileType, $id, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $proProfile = ProProfile::getByPath("/Services/$profileType/Profiles/$id");

            if (!$proProfile) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Profile not found.',
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Get projects
            $projects = $proProfile->getProjects();
            $projectsData = [];
            
            foreach ($projects as $project) {
                $projectGallery = $project->getProjectGallery();
                $featuredImage = null;
                
                // // Get the first image from the gallery if available
                // if ($projectGallery && count($projectGallery) > 0) {
                //     $featuredImage = $projectGallery[0]->getFullPath();
                // }

                // Get images from the gallery properly
                if ($projectGallery instanceof \Pimcore\Model\DataObject\Data\ImageGallery) {
                    $galleryItems = $projectGallery->getItems(); // Get an array of images
                    if (!empty($galleryItems)) {
                        $featuredImage = $galleryItems[0]->getImage()->getFullPath();
                    }
                }
                
                $projectsData[] = [
                    'id' => $project->getKey(),
                    'name' => $project->getProjectName(),
                    'location' => $project->getLocation(),
                    'featuredImage' => $featuredImage,
                ];
            }
            
            return new JsonResponse([
                'success' => true,
                'data' => $projectsData,
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $logger->error('Profile Projects API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch profile projects.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/project/{profileType}/{id}", name="project-detail-api", methods={"GET"})
     */
    public function projectDetailApiAction($profileType, $id, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $project =  ProProject::getByPath("/Services/$profileType/Projects/$id");

            if (!$project) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Project not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $projectGallery = $project->getProjectGallery();
            $galleryImages = [];
            if ($projectGallery instanceof \Pimcore\Model\DataObject\Data\ImageGallery) {
                $galleryItems = $projectGallery->getItems();
                foreach ($galleryItems as $item) {
                    $galleryImages[] = $item->getImage()->getFullPath();
                }
            }

            $projectData = [
                'id' => $project->getId(),
                'projectName' => $project->getProjectName(),
                'location' => $project->getLocation(),
                'minPrice' => $project->getMinPrice(),
                'projectDescription' => $project->getProjectDescription(),
                'galleryImages' => $galleryImages,
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $projectData,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Project Detail API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch project details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional-projects/{customerID}/{page}", name="professional-projects-api", methods={"GET"})
     */
    public function professionalProjectsApiAction($customerID, $page, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            $logger->info("Customer Portfolio: " . print_r($proProfiles, true));

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile->getKey(), true));

            // Get all projects associated with the ProProfile
            $allProjects = $proProfile->getProjects();
            $logger->info("Total projects found: " . count($allProjects));

            if (empty($allProjects)) {
                $logger->warning("No projects found for ProProfile ID: {$proProfile->getKey()}");
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                ], Response::HTTP_OK);
            }

            // Paginate the projects
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedProjects = array_slice($allProjects, $offset, $perPage);
            
            $projectsData = [];
            foreach ($paginatedProjects as $project) {
                // Get the project gallery
                $projectGallery = $project->getProjectGallery();
                $featuredImage = null;
                
                // Get the first image from the gallery if available
                if ($projectGallery instanceof \Pimcore\Model\DataObject\Data\ImageGallery) {
                    $galleryItems = $projectGallery->getItems(); // Get an array of images
                    if (!empty($galleryItems)) {
                        $featuredImage = $galleryItems[0]->getImage()->getFullPath();
                    }
                }
                
                $projectsData[] = [
                    'id' => $project->getKey(),
                    'name' => $project->getProjectName(),
                    'location' => $project->getLocation(),
                    'image' => $featuredImage, // Added featured image
                    'rating' => 18,
                    // 'rating' => $project->getRating() ?? 18, // Add rating if available or default to 18
                ];
            }

            $logger->info("Paginated projects data: " . print_r($projectsData, true));

            return new JsonResponse([
                'success' => true,
                'data' => $projectsData,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Projects API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional projects.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional/add-project/{customerID}", name="api-professional-add-project", methods={"POST"})
     */
    public function apiProfessionalAddProjectAction($customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $customertype = $customer->getcustomertype();
            $PortfolioActivate = $customer->getPortfolioActivate();
            
            // Check if user is a professional with activated portfolio
            if ($customertype !== 'Professional' || $PortfolioActivate !== 'true') {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'User is not a professional or portfolio is not activated.',
                ], Response::HTTP_FORBIDDEN);
            }
            
            // Get the professional profile
            $ProProfiles = $customer->getPortfolio();
            if (empty($ProProfiles)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Professional profile not found.',
                ], Response::HTTP_NOT_FOUND);
            }
            $ProProfile = $ProProfiles[0];
            
            // Create new project
            $ProProject = new \Pimcore\Model\DataObject\ProProject();
            $ProProject->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/Services/Professionals/Projects'));
            
            // Set project properties from request
            $projectName = $request->request->get('ProjectName');
            $projectLocation = $request->request->get('Location');
            $projectDescription = $request->request->get('ProjectDescription');
            $priceRange = $request->request->get('PriceRange');
            $configuration = $request->request->get('Configuration');
            $collaborations = $request->request->get('Collaborations');
            
            
            // Set basic project properties
            $ProProject->setProfessional($ProProfile);
            $ProProject->setKey(Text::toUrl($projectName . '-' . time()));
            $ProProject->setProjectName($projectName);
            $ProProject->setProjectDescription($projectDescription);
            $ProProject->setLocation($projectLocation);
            $ProProject->setMinPrice($priceRange);
            $ProProject->setConfiguration($configuration);
            $ProProject->setCollaborations($collaborations);
            $ProProject->setProfessionalPath($ProProfile);
            
            // Handle project gallery images
            $files = $request->files->get('ProjectGallery');
            if (!empty($files) && is_array($files)) {
                $items = [];
                
                foreach ($files as $file) {
                    if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                        $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                        
                        // Create a new image asset
                        $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $image = new \Pimcore\Model\Asset\Image();
                        $image->setFilename($imageName);
                        $image->setData(file_get_contents($file->getPathname()));
                        $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Professionals/ProjectGallery"));
                        $image->save();
                        
                        $hotspotImage->setImage($image);
                        $items[] = $hotspotImage;
                    }
                }
                
                // Set gallery to project
                if (!empty($items)) {
                    $ProProject->setProjectGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
                }
            }
            
            // Publish and save the project
            $ProProject->setPublished(true);
            $ProProject->save();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Project added successfully!',
                'projectId' => $ProProject->getId()
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $logger->error('API Add Project error: ' . $e->getMessage());
            $logger->error('Stack trace: ' . $e->getTraceAsString());
            
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while adding the project.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional-products/{customerID}/{page}", name="professional-products-api", methods={"GET"})
     */
    public function professionalProductsApiAction($customerID, $page, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile->getKey(), true));

            
            $allProducts = $proProfile->getProducts();
            $logger->info("Total products found: " . count($allProducts));

            if (empty($allProducts)) {
                $logger->warning("No products found for ProProfile ID: {$proProfile->getKey()}");
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                ], Response::HTTP_OK);
            }

            // Paginate the products
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedProducts = array_slice($allProducts, $offset, $perPage);
            
            $productsData = [];
            foreach ($paginatedProducts as $product) {
                // Get the product image
                $productImage = $product->getProductImage();
                $imageFullPath = null;
                
                if ($productImage instanceof \Pimcore\Model\Asset\Image) {
                    $imageFullPath = $productImage->getFullPath();
                }
                
                $productsData[] = [
                    'id' => $product->getKey(),
                    'name' => $product->getProductName(),
                    'price' => $product->getPrice() ? $product->getPrice() : 'Not specified',
                    'creationDate' => $product->getCreationDate() ? date('Y-m-d', $product->getCreationDate()) : null,
                    'image' => $imageFullPath,
                ];
            }

            $logger->info("Paginated products data: " . print_r($productsData, true));

            return new JsonResponse([
                'success' => true,
                'data' => $productsData,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Products API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional products.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/professional/add-product/{customerID}", name="api-professional-add-product", methods={"POST"})
     */
    public function apiProfessionalAddProductAction($customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();
            
            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }
            
            $customer = $customersList[0];
            $customertype = $customer->getcustomertype();
            
            // Check if user is a professional 
            if ($customertype !== 'Supplier') {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'User is not a Supplier.',
                ], Response::HTTP_FORBIDDEN);
            }
            
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            
            // Create new product
            $ProProduct = new \Pimcore\Model\DataObject\ProProduct();
            $ProProduct->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/Services/Professionals/Products'));
            
            // Set product properties from request
            $productName = $request->request->get('ProductName');
            $productDescription = $request->request->get('ProductDescription');
            $productPrice = $request->request->get('ProductPrice');
            $category = $request->request->get('Category');
            $brand = $request->request->get('Brand');
            
            // Set basic product properties
            $ProProduct->setKey(Text::toUrl($productName . '-' . time()));
            $ProProduct->setProductName($productName);
            $ProProduct->setProductDescription($productDescription);
            $ProProduct->setPrice($productPrice);
            $ProProduct->setParentCategory($category);
            $ProProduct->setProductBrand($brand);
            $ProProduct->setProfessional($ProProfile);

            $imageData = $request->files->get('ProductGallery[0]');

            if ($imageData) {

                // $previousimage = $ProProduct->getProductImage();
                // $previousimage->delete();

                $imageName = $imageData->getClientOriginalName();
                $imageName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                $newAsset = new Image();
                
                $newAsset->setFilename($imageName);
                
                $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/Services/Dealers/ProductGallery"));
                
                $newAsset->setData(file_get_contents($imageData->getPathname()));
                $newAsset->save();
                $ProProduct->setProductImage($newAsset);
            }  
            
            
            // Handle product gallery images
            // $files = $request->files->get('ProductGallery');

            
            // if (!empty($files) && is_array($files)) {
            //     $items = [];
                
            //     foreach ($files as $file) {
            //         if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            //             $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                        
            //             // Create a new image asset
            //             $imageName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            //             $image = new \Pimcore\Model\Asset\Image();
            //             $image->setFilename($imageName);
            //             $image->setData(file_get_contents($file->getPathname()));
            //             $image->setParent(\Pimcore\Model\Asset::getByPath("/Services/Professionals/ProductGallery"));
            //             $image->save();
                        
            //             $hotspotImage->setImage($image);
            //             $items[] = $hotspotImage;
            //         }
            //     }
                
            //     // Set gallery to product
            //     if (!empty($items)) {
            //         $ProProduct->setProductGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
            //     }
            // }
            
            // Publish and save the product
            $ProProduct->setPublished(true);
            $ProProduct->save();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Product added successfully!',
                'productId' => $ProProduct->getId()
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $logger->error('API Add Product error: ' . $e->getMessage());
            $logger->error('Stack trace: ' . $e->getTraceAsString());
            
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while adding the product.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/mobile/products", name="mobile-products-api", methods={"GET"})
     */
    public function mobileProductsApiAction(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Get filter parameters from request
            $minPrice = $request->query->getInt('minPrice', 0);
            $maxPrice = $request->query->getInt('maxPrice', 10000000);
            $sortOption = $request->query->get('sortOption', 'default');
            $page = max(1, $request->query->getInt('page', 1));
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            // Database connection
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);

            // Sorting logic
            $sortColumn = 'p.ID'; // Default sorting by ID
            if ($sortOption === 'priceLowHigh') {
                $sortColumn = 'pc.Product_Price ASC';
            } elseif ($sortOption === 'priceHighLow') {
                $sortColumn = 'pc.Product_Price DESC';
            }

            // Main query with pagination
            $sql = "
                SELECT DISTINCT 
                    p.ID, 
                    p.Unique_ID, 
                    p.Product_Name, 
                    COALESCE(pc.Product_Price, 0) AS Product_Price, 
                    COALESCE(pc.Product_Unit, '') AS Product_Unit, 
                    (SELECT product_image_Path FROM product_images WHERE Unique_ID = p.Unique_ID LIMIT 1) AS Image_Path
                FROM products p
                LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                WHERE COALESCE(pc.Product_Price, 0) BETWEEN :minPrice AND :maxPrice
                ORDER BY $sortColumn
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $stmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll();

            // Ensure unique keys in the response
            foreach ($products as &$product) {
                $product['key'] = 'product_' . $product['ID']; // Assign a unique key
            }

            // Total count query (for pagination)
            $countSql = "
                SELECT COUNT(DISTINCT p.ID) AS total 
                FROM products p
                LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                WHERE COALESCE(pc.Product_Price, 0) BETWEEN :minPrice AND :maxPrice
            ";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->bindValue(':minPrice', $minPrice, \PDO::PARAM_INT);
            $countStmt->bindValue(':maxPrice', $maxPrice, \PDO::PARAM_INT);
            $countStmt->execute();
            $totalItems = $countStmt->fetchColumn();
            $totalPages = ceil($totalItems / $perPage);

            return new JsonResponse([
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalItems' => $totalItems
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Mobile Products API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch products.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    

    /**
     * @Route("/api/mobile/categories", name="mobile-categories-api", methods={"GET"})
     */
    public function mobileCategoriesApiAction(Request $request): JsonResponse
    {
        try {
            // Fetch categories from the same JSON file as web version
            $categoriesJsonPath = PIMCORE_WEB_ROOT . '/static/files/CusProdCategories.json';
            
            if (!file_exists($categoriesJsonPath)) {
                throw new \Exception('Categories JSON file not found');
            }
            
            $categoriesData = json_decode(file_get_contents($categoriesJsonPath), true);
            
            return new JsonResponse([
                'success' => true,
                'data' => $categoriesData
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch categories.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/mobile/product-details/{uniqueId}", name="mobile-product-details-api", methods={"GET"})
     */
    public function mobileProductDetailsAction($uniqueId, LoggerInterface $logger): JsonResponse
    {
        try {
            // Database connection
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);

            // Fetch product details
            $sql = "
                SELECT 
                    p.ID, 
                    p.Unique_ID, 
                    p.Product_Name, 
                    p.Product_Brand, 
                    p.Product_Type, 
                    p.Product_Description, 
                    p.Product_Category, 
                    p.Product_Sub_SubCategory, 
                    p.Specification_1,
                    pc.Product_Price,
                    pc.Product_Unit
                FROM products p
                LEFT JOIN product_costs pc ON p.Unique_ID = pc.Unique_ID
                WHERE p.Unique_ID = :UniqueId
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':UniqueId', $uniqueId);
            $stmt->execute();
            $product = $stmt->fetch();

            // Fetch product images
            $imageSql = "
                SELECT product_image_Path 
                FROM product_images 
                WHERE Unique_ID = :UniqueId
            ";

            $imageStmt = $pdo->prepare($imageSql);
            $imageStmt->bindValue(':UniqueId', $uniqueId);
            $imageStmt->execute();
            $imageDatas = $imageStmt->fetchAll();

            // Prepare response data
            $responseData = [
                'ID' => $product['ID'],
                'Unique_ID' => $product['Unique_ID'],
                'Product_Name' => $product['Product_Name'],
                'Product_Brand' => $product['Product_Brand'],
                'Product_Type' => $product['Product_Type'],
                'Product_Description' => $product['Product_Description'],
                'Product_Category' => $product['Product_Category'],
                'Product_Sub_SubCategory' => $product['Product_Sub_SubCategory'],
                'Specification_1' => $product['Specification_1'], // This will be a JSON string
                'PriceData' => [
                    'Product_Price' => $product['Product_Price'],
                    'Product_Unit' => $product['Product_Unit']
                ],
                'ImageDatas' => $imageDatas
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $responseData
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Mobile Product Details API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch product details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional-boqs/{customerID}", name="professional-boqs-api", methods={"GET"})
     */
    public function professionalBOQsApiAction($customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . $customer->getKey());

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            
            // Get all active requirements
            $ProRequirementsList = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            $ProRequirementsList->addConditionParam("ExpiryCheck = ?", "Active");
            $ProRequirementsList->setOrderKey('creationDate');
            $ProRequirementsList->setOrder('desc');
            $activeRequirements = $ProRequirementsList->load();

            // Get customer products and tags
            $customerProducts = $proProfile->getProducts();
            $customerTags = [];
            foreach ($customerProducts as $product) {
                $tags = $product->getTags();
                $customerTags = array_merge($customerTags, array_map('trim', explode(',', $tags)));
            }

            // Categorize requirements into enabled and disabled
            $enabledRequirements = [];
            $disabledRequirements = [];
            $lowercaseCustomerTags = array_map('strtolower', $customerTags);

            foreach ($activeRequirements as $requirement) {
                $enabled = false;
                
                foreach ($requirement->getProRequirementProduct() as $product) {
                    if (in_array(strtolower($product->getProductName()), $lowercaseCustomerTags, true)) {
                        $enabled = true;
                        break;
                    }
                }

                $requirementData = [
                    'id' => $requirement->getKey(),
                    'title' => $requirement->getTitle(),
                    'expireDate' => $requirement->getExpireDate() ? $requirement->getExpireDate()->format('Y-m-d H:i:s') : null,
                    'city' => $requirement->getCity() ?: 'N/A',
                    'productsCount' => count($requirement->getProRequirementProduct()),
                ];

                if ($enabled) {
                    $enabledRequirements[] = $requirementData;
                } else {
                    $disabledRequirements[] = $requirementData;
                }
            }

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'enabledRequirements' => $enabledRequirements,
                    'disabledRequirements' => $disabledRequirements,
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional BOQs API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional BOQs.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional-boq-details/{boqId}/{customerID}", name="professional-boq-details-api", methods={"GET"})
     */
    public function professionalBOQDetailsApiAction($boqId, $customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            
            // Load the BOQ
            $ProRequirement = \Pimcore\Model\DataObject\ProRequirement::getByPath("/Requirements/{$boqId}");
            if (!$ProRequirement) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'BOQ not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Load customer products and tags
            $UserProfile = $customer->getPortfolio()[0];
            $customerProducts = $UserProfile->getProducts();
            $customerTags = [];
            foreach ($customerProducts as $product) {
                $tags = $product->getTags();
                $customerTags = array_merge($customerTags, array_map('trim', explode(',', $tags)));
            }

            // Process BOQ products
            $ProRequirementProducts = $ProRequirement->getProRequirementProduct();
            $enabledProducts = [];
            $disabledProducts = [];

            foreach ($ProRequirementProducts as $product) {
                $productData = [
                    'id' => $product->getKey(),
                    'name' => $product->getProductName(),
                    'quantity' => $product->getQuantity(),
                    'unit' => $product->getUnit(),
                    'description' => $product->getDescription(),
                    'specification' => $product->getMaterial(),
                ];

                if (in_array(strtolower($product->getProductName()), array_map('strtolower', $customerTags), true)) {
                    $enabledProducts[] = $productData;
                } else {
                    $disabledProducts[] = $productData;
                }
            }

            // Prepare BOQ details
            $boqDetails = [
                'id' => $ProRequirement->getKey(),
                'title' => $ProRequirement->getTitle(),
                'description' => $ProRequirement->getDescription(),
                'city' => $ProRequirement->getCity(),
                'expireDate' => $ProRequirement->getExpireDate() ? $ProRequirement->getExpireDate()->format('Y-m-d H:i:s') : null,
                'professional' => [
                    'id' => $ProRequirement->getProfessional()->getKey(),
                    'name' => $ProRequirement->getProfessional()->getCompanyName(),
                ],
                'enabledProducts' => $enabledProducts,
                'disabledProducts' => $disabledProducts,
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $boqDetails,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional BOQ Details API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch BOQ details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/supplier-bids/{boqId}/{customerId}", name="get-supplier-bids", methods={"GET"})
     */
    public function getSupplierBids($boqId, $customerId, LoggerInterface $logger): Response
    {
        try {
            // Load the BOQ
            // $boq = \Pimcore\Model\DataObject\ProRequirement::getByobjKey($boqId);

            $ProRequirement = new \Pimcore\Model\DataObject\ProRequirement\Listing();
            $ProRequirement->addConditionParam("ObjKey = ?", $boqId);

            $Requirement= $ProRequirement->load();


            $boq = $Requirement[0];

            if (!$boq) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'BOQ not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Load the customer
            // $customer = \Pimcore\Model\DataObject\Customer::getById($customerId);

            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerId);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerId}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];

            if (!$customer) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Get customer's portfolio
            $portfolio = $customer->getPortfolio();
            if (empty($portfolio)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'No portfolio found for this customer'
                ], Response::HTTP_NOT_FOUND);
            }

            $supplier = $portfolio[0];
            $bids = [];

            // Get all products in this BOQ
            $products = $boq->getProRequirementProduct();
            foreach ($products as $product) {
                $productBids = $product->getSupplierBid();
                foreach ($productBids as $bid) {
                    if ($bid->getSupplier() && $bid->getSupplier()->getKey() === $supplier->getKey()) {
                        $bids[] = [
                            'id' => $bid->getKey(),
                            'productKey' => $product->getKey(),
                            'bidAmount' => $bid->getBidAmount(),
                            'timeDuration' => $bid->getTimeDuration(),
                            'warrantyPeriod' => $bid->getBidWarrantyPeriod(),
                            'paymentTerms' => $bid->getBidPaymentTerms()
                        ];
                        break; // Only one bid per product per supplier
                    }
                }
            }

            return new JsonResponse([
                'success' => true,
                'bids' => $bids
            ]);

        } catch (\Exception $e) {
            $logger->error("Error fetching supplier bids: " . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Error fetching bids'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/supplier-bid/{customerID}", name="add-supplier-Bid-API", methods={"POST"})
     */
    public function createSupplierBidAPI($customerID, Request $request, Security $security, LoggerInterface $logger): Response
    {
        
        // Load the customer
        $customers = new \Pimcore\Model\DataObject\Customer\Listing();
        $customers->addConditionParam("UserID = ?", $customerID);
        $customersList = $customers->load();

        if (empty($customersList)) {
            $logger->warning("No customer found with UserID: {$customerID}");
            return new JsonResponse([
                'success' => false,
                'message' => 'Customer not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $customer = $customersList[0];
        

        $existingBid = null;
        $ProProfile = null;

        // $ProRequirementProductKey = (string)$request->request->get('productKey');

        $data = json_decode($request->getContent(), true);
        $ProRequirementProductKey = isset($data['productKey']) ? (string)$data['productKey'] : null;

        $logger->info("Product Key received: " . $ProRequirementProductKey);
        $ProRequirementProducts = new \Pimcore\Model\DataObject\ProRequirementProduct\Listing();
        $ProRequirementProducts->addConditionParam("ObjKey = ?", $ProRequirementProductKey);

        $RequirementProducts = $ProRequirementProducts->load();

        if (empty($RequirementProducts)) {
            $logger->warning("No RequirementProduct Found");
            return new JsonResponse([
                'success' => false,
                'message' => 'Requirement not found.',
            ], Response::HTTP_NOT_FOUND);
        }


        $ProRequirementProduct = $RequirementProducts[0];

        // $ProRequirementProduct = \Pimcore\Model\DataObject\ProRequirementProduct::getById($ProRequirementProductKey);

        $ProRequirement = $ProRequirementProduct->getProRequirement();
        $Professional = $ProRequirement->getProfessional();
        $ownerCustomer = $Professional->getCustomer();

        $supplierBids = $ProRequirementProduct->getSupplierBid();

        
        $customertype = $customer->getcustomertype();
        $customerActivate = $customer->getPortfolioActivate();
        if($customerActivate === 'true'){
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
        }


        // Loop through the existing SupplierBids
        foreach ($supplierBids as $supplierBid) {
            if ($supplierBid->getSupplier()->getKey() === $ProProfile->getKey()) {
                $existingBid = $supplierBid;
                break; // Exit the loop as we found a match
            }
        }


        if ($existingBid) {
            $bidAmount = $data['bidAmount'];
            // $expiryDate = $data['deliveryTime'];
            $TimeDuration = $data['timeDuration'];
            $WarrantyPeriod = $data['warrantyPeriod'];
            $PaymentTerms = $data['paymentTerms'];
            // if ($expiryDate) {
            //     try {
            //         $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
            //         $expiryCarbonDate = Carbon::instance($expiryDateTime);
            //         $existingBid->setEndDate($expiryCarbonDate);
            //     } catch (\Exception $e) {
            //         error_log("Error converting expiry date: " . $e->getMessage());
            //         // Handle the error, maybe log it or return an error response
            //         // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
            //     }
            // }

            $existingBid->setBidAmount($bidAmount);
            $existingBid->settimeDuration($TimeDuration);
            $existingBid->setbidWarrantyPeriod($WarrantyPeriod);
            $existingBid->setbidPaymentTerms($PaymentTerms);
            $existingBid->save(); 

            $Notification = new ProNotification();
            $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
            $NotrandomNumber = rand(1000, 9999); 
            $uniqueKey = $NotrandomNumber . '-' . time();
            $Notification->setKey(Text::toUrl($uniqueKey));
            $Notification->setMessage("Quote Modified on your BOQ");
            $Notification->setDescription("Click to view Quote");
            $Notification->setCustomer($ownerCustomer);
            $redirecturl = '/BOQ/customize/'.$ProRequirement->getKey();
            $Notification->seturl($redirecturl);
            $Notification->setPublished(true);
            $Notification->save();
        }

        else{
            $productName = $data['productName'];
            // $productBrand = $data['productBrand'];
            $productQuantity = $data['productQuantity'];
            $productUnit = $data['productUnit'];
            // $productMaterial = $data['productMeterial'];
            $bidAmount = $data['bidAmount'];

            $TimeDuration = $data['timeDuration'];
            $WarrantyPeriod = $data['warrantyPeriod'];
            $PaymentTerms = $data['paymentTerms'];
            
            // $supplierPinnedNotificationPath = $request->request->get('SupplierPinnedNotificationPath');
            // $expiryDate = $data['deliveryTime'];$request->request->get('deliveryTime');

            if ($ProProfiles) {
                    
                // Create a new SupplierPinnedNotification for each matching tag
                $supplierBid = new SupplierBid();
                $supplierBid->setProductName($productName);
                // $supplierBid->setProductBrand($productBrand);
                $supplierBid->setProductQuantity($productQuantity);
                $supplierBid->setProductUnit($productUnit);
                // $supplierBid->setMaterial($productMaterial);
                $supplierBid->setBidAmount($bidAmount);

                $supplierBid->settimeDuration($TimeDuration);
                $supplierBid->setbidWarrantyPeriod($WarrantyPeriod);
                $supplierBid->setbidPaymentTerms($PaymentTerms);

                // $supplierBid->setSupplierPinnedNotification($supplierPinnedNotification);
                $supplierBid->setProRequirementProduct($ProRequirementProduct);
                $supplierBid->setSupplier($ProProfile); // Set the Supplier field to the current ProProfile
            

                // if ($expiryDate) {
                //     try {
                //         $expiryDateTime = new \DateTime($expiryDate, new \DateTimeZone('Asia/Kolkata'));
                //         $expiryCarbonDate = Carbon::instance($expiryDateTime);
                //         $supplierBid->setEndDate($expiryCarbonDate);
                //     } catch (\Exception $e) {
                //         error_log("Error converting expiry date: " . $e->getMessage());
                //         // Handle the error, maybe log it or return an error response
                //         // Example: return new JsonResponse(['error' => 'Invalid date format'], 400);
                //     }
                // }

                $supplierBid->setParent(Service::createFolderByPath('/SupplierBid'));
                $supplierBid->setKey(uniqid());
                $supplierBid->setPublished(true);
                $supplierBid->save();

                
                


                $Notification = new ProNotification();
                $Notification->setParent(Service::createFolderByPath('/Services/Notifications'));
                $NotrandomNumber = rand(1000, 9999); 
                $uniqueKey = $NotrandomNumber . '-' . time();
                $Notification->setKey(Text::toUrl($uniqueKey));
                $Notification->setMessage("You Have Recieved a New Quote on your BOQ");
                $Notification->setDescription("Click to view Quote");
                $Notification->setCustomer($ownerCustomer);
                $redirecturl = '/BOQ/customize/'.$ProRequirement->getKey();
                $Notification->seturl($redirecturl);
                $Notification->setPublished(true);
                $Notification->save();



                $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $EmailTemplates->addConditionParam("TemplateName = ?", "QuoteReceivedEmail");
                $EmailTemplate = $EmailTemplates->load();
                $EmailTemplate = $EmailTemplate[0];

                $subject = $EmailTemplate->getSubject();
                $htmlContent = $EmailTemplate->getContent();
                eval("\$htmlContent = \"$htmlContent\";");
                // Create a new Pimcore\Mail instance
                $mail = new \Pimcore\Mail();
                // $mail->from('arqonztest@gmail.com');
                $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                $mail->to($ownerCustomer->getEmail());
                $mail->subject($subject);
                $mail->html($htmlContent);
                $mail->send();

                // $supplierPinnedNotification->setStatus('Accepted');
                // $supplierPinnedNotification->setCurrentBid($supplierBid);
                // $supplierPinnedNotification->save();
                

            }

        }


        return new JsonResponse(['success' => true]);
        
    }


    /**
     * @Route("/api/professional-enquiries/{customerID}/{page}", name="professional-enquiries-api", methods={"GET"})
     */
    public function professionalEnquiriesApiAction($customerID, $page, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            $logger->info("Customer Portfolio: " . print_r($proProfiles, true));

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile->getKey(), true));

            // Get all endorsements associated with the customer
            $allEnquiries = $proProfile->getEnquiries();
            $logger->info("Total enquiries found: " . count($allEnquiries));

            if (empty($allEnquiries)) {
                $logger->warning("No enquiries found for customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                ], Response::HTTP_OK);
            }

            // Paginate the endorsements
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedEnquiries = array_slice($allEnquiries, $offset, $perPage);
            
            $enquiriesData = [];
            foreach ($paginatedEnquiries as $enquiry) {
                // $status = $endorsement->getStatus() ?: 'Requested';
                
                $enquiriesData[] = [
                    'key' => $enquiry->getKey(),
                    'name' => $enquiry->getfullname(),
                    'city' => $enquiry->getCity(),
                    'unlock' => $enquiry->getUnlock(),
                    'date' => $enquiry->getCreationDate() ? 
                        (new \DateTime())->setTimestamp($enquiry->getCreationDate())->format('d-m-Y') : 'N/A',
                ];
            }

            $logger->info("Paginated endorsements data: " . print_r($enquiriesData, true));

            return new JsonResponse([
                'success' => true,
                'data' => $enquiriesData,
                'pagination' => [
                    'total' => count($allEnquiries),
                    'page' => (int)$page,
                    'perPage' => $perPage,
                    'totalPages' => ceil(count($allEnquiries) / $perPage)
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Endorsements API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional endorsements.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/enquiry-details/{enquiryId}/{customerID}", name="enquiry-details-api", methods={"GET"})
     */
    public function enquiryDetailsApiAction($enquiryId, $customerID, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            $proProfile = $proProfiles[0];
            $portFolioType = $proProfile->getPortfolioType();

            $logger->info("ProProfile typpe Found " . print_r($portFolioType, true));

            $ProEnquiry = ProEnquiry::getByPath("/Services/$portFolioType"."s/Enquiries/$enquiryId");

            $logger->info("ProEnquiry found: " . print_r($ProEnquiry->getKey(), true));

            if (!$ProEnquiry) {
                throw $this->createNotFoundException('Invalid Enquiry URL');
            }


            $enquiry = $ProEnquiry;
            $logger->info("Enquiry found: " . print_r($enquiry->getKey(), true));


            $enquiryData = [
                'fullname' => $enquiry->getfullname(),
                'city' => $enquiry->getCity(),
                'message' => $enquiry->getMessage(),
                'status' => $enquiry->getUnlock(),
                'phone' => $enquiry->getUnlock() ? $enquiry->getPhone() : null,
                'email' => $enquiry->getUnlock() ? $enquiry->getEmail() : null,
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $enquiryData,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Enquiry Details API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch enquiry details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/{PortfolioType}/submit-enquiry", name="submit-enquiry-api", methods={"POST"})
     */
    public function submitEnquiryApiAction($PortfolioType, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validate the incoming data
            if (empty($data['fullname']) || empty($data['email']) || empty($data['phone']) || empty($data['message']) || empty($data['profileId'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'All fields are required.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $profileId = $data['profileId'];



            // Load the profile
            $proProfile = ProProfile::getByPath("/Services/$PortfolioType/Profiles/$profileId");
            // $proProfile = ProProfile::getByPath($data['profileId']);

            if (!$proProfile) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Profile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $proProfile->getCustomer();

            // Create and save the enquiry
            $ProEnquiry = new ProEnquiry();
            $ProEnquiry->setParent(Service::createFolderByPath("/Services/$PortfolioType/Enquiries"));
            $ProEnquiry->setProfessional($proProfile);
            $ProEnquiry->setKey(Text::toUrl(time()));
            $ProEnquiry->setFullname($data['fullname']);
            $ProEnquiry->setEmail($data['email']);
            $ProEnquiry->setPhone($data['phone']);
            $ProEnquiry->setMessage($data['message']);

            if (!empty($data['city'])) {
                $ProEnquiry->setCity($data['city']);
            }

            $ProEnquiry->setPublished(true);
            $ProEnquiry->save();

            // Send email notification
            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "EnquiryRecievedEmail");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            $htmlContent = str_replace("[Customer Name]", $data['fullname'], $htmlContent);
            $htmlContent = str_replace("{customerFirstName}", $customer->getFirstName(), $htmlContent);

            $mail = new \Pimcore\Mail();
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();

            return new JsonResponse([
                'success' => true,
                'message' => 'Enquiry submitted successfully.',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Enquiry Submission API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to submit enquiry.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/professional-requirements/{customerID}/{page}", name="professional-requirements-api", methods={"GET"})
     */
    public function professionalRequirementsApiAction($customerID, $page, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            $logger->info("Customer Portfolio: " . print_r($proProfiles, true));

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile->getKey(), true));

            // Get all requirements associated with the ProProfile
            $allRequirements = $proProfile->getRequirements();
            $logger->info("Total requirements found: " . count($allRequirements));

            if (empty($allRequirements)) {
                $logger->warning("No requirements found for ProProfile ID: {$proProfile->getKey()}");
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                ], Response::HTTP_OK);
            }

            // Paginate the requirements
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedRequirements = array_slice($allRequirements, $offset, $perPage);
            
            $requirementsData = [];
            foreach ($paginatedRequirements as $requirement) {
                $requirementsData[] = [
                    'key' => $requirement->getKey(),
                    'title' => $requirement->getTitle(),
                    'location' => $requirement->getLocation(),
                    // 'creationDate' => $requirement->getCreationDate() ? $requirement->getCreationDate()->format('d-m-Y') : 'N/A',
                    'creationDate' => $requirement->getCreationDate() ? (new \DateTime())->setTimestamp($requirement->getCreationDate())->format('d-m-Y') : 'N/A',
                ];
            }

            $logger->info("Paginated requirements data: " . print_r($requirementsData, true));

            return new JsonResponse([
                'success' => true,
                'data' => $requirementsData,
                'pagination' => [
                    'total' => count($allRequirements),
                    'page' => (int)$page,
                    'perPage' => $perPage,
                    'totalPages' => ceil(count($allRequirements) / $perPage)
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Requirements API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional requirements.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/account/delete-requirement/{id}", name="delete-requirement", methods={"POST"})
     */
    public function deleteRequirementAction(string $id, Request $request, Security $security, LoggerInterface $logger)
    {
        $user = $security->getUser();
        
        if (!$user || !$this->isGranted('ROLE_USER')) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Get the requirement
            
            $requirement = ProRequirement::getByPath("/Requirements/$id");
            
            if (!$requirement) {
                return $this->json(['success' => false, 'message' => 'Requirement not found'], 404);
            }

            // Verify ownership (user can only delete their own requirements)
            $customer = $user;
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            
            if ($requirement->getProfessional()->getkey() !== $ProProfile->getkey()) {
                return $this->json(['success' => false, 'message' => 'Unauthorized to delete this requirement'], 403);
            }

            // Delete all related ProRequirementProduct objects
            $products = $requirement->getProRequirementProduct();
            if ($products) {
                foreach ($products as $product) {
                    $product->delete();
                }
            }

            // Delete the Excel file asset if it exists
            $excelFile = $requirement->getExcelFile();
            if ($excelFile) {
                $excelFile->delete();
            }

            // Finally delete the requirement itself
            $requirement->delete();

            return $this->json(['success' => true, 'message' => 'Requirement deleted successfully']);

        } catch (\Exception $e) {
            $logger->error('Error deleting requirement: ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Error deleting requirement'], 500);
        }
    }



    /**
     * @Route("/api/add-requirement", name="add-requirement-api", methods={"POST"})
     */
    public function addRequirementApiAction(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Get the uploaded file
            $uploadedFile = $request->files->get('excelFile');
            
            // Validate the file
            if (!$uploadedFile) {
                throw new \Exception('No file uploaded');
            }

            // Get other form data
            $title = $request->request->get('Title');
            $city = $request->request->get('City');
            $description = $request->request->get('Description');
            $customerID = $request->request->get('customerID');
            $targetPrice = $request->request->get('TargetPrice');
            $expireDate = $request->request->get('ExpireDate');

            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();
            


            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $customersList = $customers->load();
            $ProProfiles = $customer->getPortfolio();
            $ProProfile = $ProProfiles[0];
            $customertype = $ProProfile->getPortfolioType();

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
                'Dealer' => '/Services/Dealers/Requirements',
                'Distributor' => '/Services/Distributors/Requirements',
                'Manufacturer' => '/Services/Manufacturers/Requirements',
                'Engineer' => '/Services/Engineers/Requirements',
                'Professional' => '/Services/Professionals/Requirements',
                'Supplier' => '/Services/Suppliers/Requirements',
                
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
            $proRequirement->setTitle($title);
            $proRequirement->setDescription($description);
            $proRequirement->setCity($city);
            $proRequirement->setProfessional($ProProfile);
            // $proRequirement->setProfessionalPath($ProProfile);
            $excelData = $this->processExcelData($uploadedFile);
            $proRequirement->setExcelData($excelData);
            $TargetPrice = $targetPrice;
            if ($TargetPrice) {
                $proRequirement->setTargetPrice($TargetPrice);
            }

            // Handle ExpireDate
            // $expireDate = $expireDate;
            // if ($expireDate) {
            //     $expireDate = Carbon::instance($expireDate)->setTimezone('Asia/Kolkata');
            //     $proRequirement->setExpireDate($expireDate);
            // }

            if ($expireDate) {
                $expireDate = Carbon::parse($expireDate)->setTimezone('Asia/Kolkata'); 
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
                    $proRequirementProduct->setProdType($rowData[4]);
                    $proRequirementProduct->setMinDec($rowData[11]);
                    $proRequirementProduct->setQuantity($rowData[6]);
                    $proRequirementProduct->setUnit($rowData[7]);
                    $proRequirementProduct->setMinimumReserve($rowData[12]);
                    $proRequirementProduct->setDescription($rowData[5]);
                    $proRequirementProduct->setProRequirement($proRequirement); // Set the relation to ProRequirement
                    // Handle ExpireDate
                    // $expireDate = $form->get('ExpireDate')->getData();
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

            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "BOQUploadSuccess");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            $htmlContent = str_replace("[Professional Name]", $ProProfile->getCompanyName(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($customer->getEmail());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();

            return new JsonResponse([
                'success' => true,
                'message' => 'Requirement submitted successfully',
            ]);

        } catch (\Exception $e) {
            $logger->error('Error submitting requirement: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to submit requirement: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/view-requirement/{requirementId}/{customerId}", name="view_requirement_api", methods={"GET"})
     */
    public function viewRequirementApiAction($requirementId, $customerId, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Validate the customer
            $customer = \Pimcore\Model\DataObject\Customer::getByUserId($customerId, 1);
            
            if (!$customer) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Load the requirement
            $requirement = \Pimcore\Model\DataObject\ProRequirement::getByPath("/Requirements/$requirementId");
            
            if (!$requirement) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Requirement not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Verify the requirement belongs to the customer's pro profile
            $proProfile = $requirement->getProfessional();
            $customerProProfiles = $customer->getPortfolio();
            
            if (!in_array($proProfile, $customerProProfiles)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'You are not authorized to view this requirement.',
                ], Response::HTTP_FORBIDDEN);
            }

            // Get requirement products
            $requirementProducts = $requirement->getProRequirementProduct();
            
            // Prepare products data
            $productsData = [];
            foreach ($requirementProducts as $product) {
                $productsData[] = [
                    'key' => $product->getKey(),
                    'productName' => $product->getProductName(),
                    'brand' => $product->getBrand(),
                    'material' => $product->getMaterial(),
                    'quantity' => $product->getQuantity(),
                    'unit' => $product->getUnit(),
                    'imagePath' => $this->getProductImagePath($product),
                    'l1' => $product->getL1(),
                    'l2' => $product->getL2(),
                    'l3' => $product->getL3(),
                    'l1Amt' => $product->getL1Amt(),
                    'l2Amt' => $product->getL2Amt(),
                    'l3Amt' => $product->getL3Amt(),
                ];
            }

            // Prepare response data
            $responseData = [
                'key' => $requirement->getKey(),
                'title' => $requirement->getTitle(),
                'location' => $requirement->getLocation(),
                'creationDate' => $requirement->getCreationDate() ? 
                    (new \DateTime())->setTimestamp($requirement->getCreationDate())->format('d-m-Y') : 'N/A',
                'products' => $productsData
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $responseData
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('View Requirement API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch requirement details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @Route("/mobile-generate-quote", name="Mobile_generate_quote", methods={"POST"})
     */
    public function MobilegenerateQuote(Request $request, Security $security, LoggerInterface $logger): Response
    {   

            $customerID = $request->request->get('customerID');

            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];

            $customerActivate = $customer->getPortfolioActivate();
            if($customerActivate === 'true'){
                $ProProfiles = $customer->getPortfolio();
                $ProProfile = $ProProfiles[0];
            }

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
            $OrgCity = $selectedRequirement->getCity();

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

            $preset_date = (new \DateTime())->format('M j, Y');
            $future_date = (new \DateTime())->modify('+15 days')->format('M j, Y');
            
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
                                        <div>Quote No #: A00364</div>
                                        <div>Quote Date #: '.$preset_date.'</div>
                                        <div>Valid Till: '.$future_date.'</div>
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
                                                <div><b>Phone:</b> +91 9150202745</div>
                                                
                                            </div>
                                        </div>
                                    </td>
                                    <td class="billedto">
                                        <div class="toaddress">
                                            <h2>Billed To</h2>
                                            <div><b>'.$ProProfile->getCompanyName().'</b></div>
                                                <div class="companyinfo" style="font-size:12px;">
                                                    <div>'.$ProProfile->getStreetAddress().', '.$ProProfile->getCity().', '.$ProProfile->getState().', '.$ProProfile->getCountry().' - '.$ProProfile->getPinCode().'</div>
                                                    <div><b>GSTIN:</b> '.$ProProfile->getgstnumber().'</div>
                                                    <div><b>Phone:</b>+91 '.$ProProfile->getPhoneNumber().'</div>
                                                </div>
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
                $OrgUnit = $product->getUnit();
                $OrgType = $product->getProdType();

                $logger->info('Processing product', [
                    'productName' => $productName,
                    'brand' => $brand,
                    'material' => $material,
                    'quantity' => $quantity
                ]);


                
                $sql = "SELECT Unique_ID FROM products
                    WHERE Product_Name LIKE :productName
                    AND Product_Brand = :brand";
                    

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':productName', '%' . $productName . '%');
                $stmt->bindValue(':brand', $brand);
            
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
                $logger->info('Unit price and unit before ChatGPT', ['unitPrice' => $unitPrice]);

                $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
                $apiKey = $openaiConfig['api_key'];
                $logger->info('Checking condition for unitPrice', ['unitPrice' => $unitPrice]);


                if (trim($unitPrice) === 'N/A') {
                    $logger->info('Calling ChatGPT for unit price prediction', [
                        'productName' => $productName,
                        'brand' => $brand
                    ]);
                
                    // Prepare the prompt
                    $prompt = <<<PROMPT
                    Act as an expert price predictor in the construction industry who knows the exact price of construction products per Unit. You will act like a calculator and only respond in the following format: 'Price: XXXRs, Unit: XX' (e.g., 'Price: 3500Rs, Unit: Kg (The Unit Should be Same as the Unit Given in the Prompt below)'). The Price Should be Per Unit Given. If you don't have the exact price, predict the price based on the city and other details provided.
                    
                    Below is the product name and city:
                    {
                    Product Name: [$productName],
                    Unit: [$OrgUnit],
                    Brand Name: [$brand],
                    Type: [$OrgType],
                    City: [$OrgCity]
                    }
                    PROMPT;

                    // Call OpenAI API
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: Bearer $apiKey",
                        
                    ]);

                    // Prepare the request payload
                    $data = [
                        "model" => "gpt-4o-mini", // Replace with the model you're using
                        "messages" => [
                            ["role" => "user", "content" => $prompt]
                        ],
                        "temperature" => 0.7,
                        "max_tokens" => 50
                    ];

                    $logger->info('Sending to ChatGPT API', [
                        'url' => "https://api.openai.com/v1/chat/completions",
                        'headers' => [
                            "Content-Type: application/json",
                            "Authorization: Bearer $apiKey"
                        ],
                        'payload' => json_encode($data)
                    ]);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    $response = curl_exec($ch);

                    

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $logger->info('HTTP Status Code', ['code' => $httpCode]);
                    $logger->info('HTTP Response ChatGPT', ['code' => $response]);

                    if ($httpCode !== 200) {
                        error_log("Non-200 HTTP Response: $httpCode");
                    }
                    
                    
                    if ($response === false) {
                        $error = curl_error($ch);
                        error_log("CURL Error: $error");
                    } else {
                        error_log("API Response: $response");
                    }
                    curl_close($ch);

                    // Decode the response
                    $responseData = json_decode($response, true);

                    if (isset($responseData['choices'][0]['message']['content'])) {
                        $apiResponse = $responseData['choices'][0]['message']['content'];

                        $logger->info("ChatGPT Response Content", ['content' => $apiResponse]);

                        // Extract Price and Unit using regular expressions
                        if (preg_match('/Price:\s*(\d+)Rs,\s*Unit:\s*(\w+)/', $apiResponse, $matches)) {
                            $unitPrice = $matches[1]; // Example: 60Rs
                            $unit = $matches[2]; // Example: Sq.ft
                        } else {
                            // Handle invalid response
                            $unitPrice = 'N/A';
                            $unit = 'N/A';
                            error_log("Failed to extract price and unit from OpenAI response: $apiResponse");
                        }
                        $logger->info('Chat GPT Price Output', [
                            'productName' => $productName,
                            'brand' => $brand,
                            'unit price' => $unitPrice,
                            'Unit' => $unit
                        ]);
                    }
                }
                

                

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
                            <td style="text-align:left;">' . $productName .'-'. $OrgType .'</td>
                            <td style="text-align:left;">' . $brand . '</td>
                            
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
     * @Route("/api/professional-endorsements/{customerID}/{page}", name="professional-endorsements-api", methods={"GET"})
     */
    public function professionalEndorsementsApiAction($customerID, $page, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            $logger->info("Customer found: " . print_r($customer->getKey(), true));

            // Load the ProProfile associated with the customer
            $proProfiles = $customer->getPortfolio();
            $logger->info("Customer Portfolio: " . print_r($proProfiles, true));

            if (empty($proProfiles)) {
                $logger->warning("No portfolio found for Customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ProProfile not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $proProfile = $proProfiles[0];
            $logger->info("ProProfile found: " . print_r($proProfile->getKey(), true));

            // Get all endorsements associated with the customer
            $allEndorsements = $customer->getEndorsement();
            $logger->info("Total endorsements found: " . count($allEndorsements));

            if (empty($allEndorsements)) {
                $logger->warning("No endorsements found for customer ID: {$customerID}");
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                ], Response::HTTP_OK);
            }

            // Paginate the endorsements
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedEndorsements = array_slice($allEndorsements, $offset, $perPage);
            
            $endorsementsData = [];
            foreach ($paginatedEndorsements as $endorsement) {
                // $status = $endorsement->getStatus() ?: 'Requested';
                
                $endorsementsData[] = [
                    'key' => $endorsement->getKey(),
                    'name' => $endorsement->getName(),
                    'email' => $endorsement->getEmail(),
                    'phone' => $endorsement->getPhone(),
                    'Q3' => $endorsement->getQ3(),
                    // 'status' => $status,
                    'date' => $endorsement->getCreationDate() ? 
                        (new \DateTime())->setTimestamp($endorsement->getCreationDate())->format('d-m-Y') : 'N/A',
                ];
            }

            $logger->info("Paginated endorsements data: " . print_r($endorsementsData, true));

            return new JsonResponse([
                'success' => true,
                'data' => $endorsementsData,
                'pagination' => [
                    'total' => count($allEndorsements),
                    'page' => (int)$page,
                    'perPage' => $perPage,
                    'totalPages' => ceil(count($allEndorsements) / $perPage)
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $logger->error('Professional Endorsements API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch professional endorsements.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @Route("/api/request-endorsement", name="request-endorsement-api", methods={"POST"})
     */
    public function requestEndorsementApiAction(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Get form data
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
            // $Professional = $request->request->get('Professional');
            $customerID = $request->request->get('customerID');
            
            if (!$name || !$email || !$customerID) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Missing required fields (name, email, or customerID).',
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();
            
            if (empty($customersList)) {
                $logger->warning("No customer found with UserID: {$customerID}");
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }
            
            $customer = $customersList[0];
            
            // Create a new endorsement request
            $proEndorsementRequest = new ProEndorsementRequest();
            $proEndorsementRequest->setParent(Service::createFolderByPath('/Endorsement/Endorsement'));
            $proEndorsementRequest->setKey(Text::toUrl(time()));
            $proEndorsementRequest->setName($name);
            $proEndorsementRequest->setEmail($email);
            $proEndorsementRequest->setPhone($phone);
            // $proEndorsementRequest->setProfessional($Professional);
            // $proEndorsementRequest->setStatus('Requested');
            
            // Set professional if available
            if ($customer->getPortfolioActivate() === 'true') {
                $proProfiles = $customer->getPortfolio();
                if (!empty($proProfiles)) {
                    $proProfile = $proProfiles[0];
                    $proEndorsementRequest->setProfessional($proProfile);
                }
            }
            
            // Send Email
            try {
                $emailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
                $emailTemplates->addConditionParam("TemplateName = ?", "EndorsementRequest");
                $emailTemplate = $emailTemplates->load();
                
                if (!empty($emailTemplate)) {
                    $emailTemplate = $emailTemplate[0];
                    $endorsementUrl = "<a href='https://arqonz.com/user/{$customer->getUserID()}/endorsement' style='text-align:center;'>Click Here to Endorse Now</a>";
                    
                    $subject = $emailTemplate->getSubject();
                    $subject = str_replace("[customer]", $customer->getFirstname(), $subject);
                    
                    $htmlContent = $emailTemplate->getContent();
                    $htmlContent = str_replace("[Refferer]", $name, $htmlContent);
                    $htmlContent = str_replace("[customer]", $customer->getfirstname(), $subject);
                    $htmlContent = str_replace("[EndorsementURL]", $endorsementUrl, $htmlContent);
                    
                    // Create a new Pimcore\Mail instance
                    $mail = new Mail();
                    $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
                    $mail->to($email);
                    $mail->subject($subject);
                    $mail->html($htmlContent);
                    $mail->send();
                    
                    $logger->info("Endorsement request email sent to: {$email}");
                }
            } catch (\Exception $e) {
                $logger->error("Failed to send endorsement email: " . $e->getMessage());
                // Continue with saving the request even if email fails
            }
            
            // Save the endorsement request
            $proEndorsementRequest->setPublished(true);    
            $proEndorsementRequest->save();
            
            // Add the new endorsement request to the customer's endorsements
            // $currentEndorsements = $customer->getEndorsement() ?: [];
            // $currentEndorsements[] = $proEndorsementRequest;
            // $customer->setEndorsement($currentEndorsements);
            $customer->save();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Endorsement request sent successfully.',
                'data' => [
                    'endorsementId' => $proEndorsementRequest->getId()
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $logger->error('Professional Endorsement Request API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create endorsement request.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @Route("/api/customer-notifications/{customerID}/{page}/{limit}", name="customer-notifications-api", methods={"GET"})
     */
    public function customerNotificationsApiAction($customerID, $page = 1, $limit = 10, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the customer
            $customers = new \Pimcore\Model\DataObject\Customer\Listing();
            $customers->addConditionParam("UserID = ?", $customerID);
            $customersList = $customers->load();

            if (empty($customersList)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $customer = $customersList[0];
            
            // Get all notifications for the customer
            $notifications = $customer->getNotifications();
            
            if (empty($notifications)) {
                return new JsonResponse([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'unreadCount' => 0
                ]);
            }

            // Sort notifications by creation date (newest first)
            usort($notifications, function($a, $b) {
                return $b->getCreationDate() <=> $a->getCreationDate();
            });

            // Calculate unread count
            $unreadCount = 0;
            foreach ($notifications as $notification) {
                if ($notification->getReadStatus() === 'unread') {
                    $unreadCount++;
                }
            }

            // Paginate the notifications
            $offset = ($page - 1) * $limit;
            $paginatedNotifications = array_slice($notifications, $offset, $limit);
            
            $notificationsData = [];
            foreach ($paginatedNotifications as $notification) {
                // Handle both DateTime object and timestamp cases
                $creationDate = $notification->getCreationDate();
                
                if (is_numeric($creationDate)) {
                    // It's a timestamp - create DateTime object
                    $dateTime = new \DateTime();
                    $dateTime->setTimestamp($creationDate);
                    $formattedDate = $dateTime->format('Y-m-d H:i:s');
                    $timeAgo = $this->getTimeAgo($dateTime);
                } elseif ($creationDate instanceof \DateTime) {
                    // It's already a DateTime object
                    $formattedDate = $creationDate->format('Y-m-d H:i:s');
                    $timeAgo = $this->getTimeAgo($creationDate);
                } else {
                    // Fallback if creationDate is neither
                    $formattedDate = 'N/A';
                    $timeAgo = 'N/A';
                }

                $notificationsData[] = [
                    'id' => $notification->getKey(),
                    'message' => $notification->getMessage(),
                    'readStatus' => $notification->getReadStatus(),
                    'url' => $notification->getUrl(),
                    'creationDate' => $formattedDate,
                    'timeAgo' => $timeAgo
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $notificationsData,
                'total' => count($notifications),
                'unreadCount' => $unreadCount,
                'currentPage' => $page,
                'perPage' => $limit
            ]);

        } catch (\Exception $e) {
            $logger->error('Customer Notifications API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch notifications.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/api/mark-notification-read/{notificationId}", name="mark-notification-read-api", methods={"POST"})
     */
    public function markNotificationReadApiAction($notificationId, Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            // Load the notification
             
            $notification = ProNotification::getByPath("/Services/Notifications/$notificationId");
            
            if (!$notification) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Notification not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update read status
            $notification->setReadStatus('read');
            $notification->save();

            return new JsonResponse([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);

        } catch (\Exception $e) {
            $logger->error('Mark Notification Read API error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to update notification status.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getTimeAgo(\DateTime $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    }


    /**
     * @Route("/deal-desk", name="deal_desk_landing")
     */
    public function dealDeskLandingAction(Request $request): Response
    {
        // Fetch all DealDeskBuilder objects
        $builders = new DealDeskBuilder\Listing();
        
        
        
        // Event details - could also be moved to a configuration or Pimcore object
        $eventDetails = [
            'date' => '26th July, 2026',
            'time' => '10:00 AM – 5:00 PM',
            'lunchBreak' => '1:00 – 2:00 PM',
            'location' => 'ArQonZ DealDesk, Bengaluru Tech Hub'
        ];

        return $this->render('Professional/deal_desk_landing.html.twig', [
            'builders' => $builders->load(),
            'eventDetails' => $eventDetails,
            'pageTitle' => 'ArQonZ DealDesk - Construction Deal Making Event'
        ]);
    }


    /**
     * @Route("/deal-desk/{key}", name="deal_desk_builder_details")
     */
    public function dealDeskBuilderDetailsAction(Request $request, string $key, LoggerInterface $logger, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            // Try to load the DealDeskBuilder object by path
            $builder = DealDeskBuilder::getByPath("/DealDeskbuilders/$key");
            
            if (!$builder) {
                throw $this->createNotFoundException('Builder not found');
            }

            // Parse materials string into an array for easier display
            $materialsString = $builder->getmaterials() ? trim($builder->getmaterials(), "'") : '';
            $materialsArray = $materialsString ? explode("', '", $materialsString) : [];

            // Parse time slots JSON with proper error handling
            $timeSlots = [];
            $timeSlotsJson = $builder->getTimeSlots();
            
            if (!empty($timeSlotsJson)) {
                try {
                    $timeSlots = json_decode($timeSlotsJson, true, 512, JSON_THROW_ON_ERROR);
                    
                    // Validate the decoded JSON structure
                    if (!isset($timeSlots['time_slots']) || !is_array($timeSlots['time_slots'])) {
                        throw new \RuntimeException('Invalid time slots JSON structure');
                    }
                } catch (\JsonException $e) {
                    // Log the error and use default time slots
                    error_log('Failed to decode time slots JSON: ' . $e->getMessage());
                    
                    // Set default time slots structure
                    $timeSlots = [
                        'time_slots' => [],
                        'break' => [
                            'time_slot' => '1:00PM - 2:00PM',
                            'description' => 'Lunch Break'
                        ]
                    ];
                }
            } else {
                // If timeSlots is empty, initialize with default structure
                $timeSlots = [
                    'time_slots' => [],
                    'break' => [
                        'time_slot' => '1:00PM - 2:00PM',
                        'description' => 'Lunch Break'
                    ]
                ];
            }

            // Event details (same as landing page)
            $eventDetails = [
                'date' => '26th July, 2026',
                'time' => '10:00 AM – 5:00 PM',
                'lunchBreak' => '1:00 – 2:00 PM',
                'location' => 'ArQonZ DealDesk, Bengaluru Tech Hub'
            ];

            // Determine which template to use based on the key
            $template = 'Professional/deal_desk_builder_details.html.twig';
            
            switch ($key) {
                case 'gt-bharathi-urban-developers':
                    $template = 'Professional/deal_desk_gt_bharathi.html.twig';
                    break;
                case 'puravankara':
                    $template = 'Professional/deal_desk_puravankara.html.twig';
                    break;
                case 'shree-padam-foundation':
                    $template = 'Professional/deal_desk_shree_padam.html.twig';
                    break;
                case 'sunshine-builders':
                    $template = 'Professional/deal_desk_sunshine.html.twig';
                    break;
            }

            return $this->render($template, [
                'builder' => $builder,
                'materials' => $materialsArray,
                'timeSlots' => $timeSlots,
                'eventDetails' => $eventDetails,
                'pageTitle' => $builder->getName() . ' | ArQonZ DealDesk'
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/deal-desk/book-slot/{key}", name="deal_desk_book_slot", methods={"POST"})
     */
    public function bookSlotAction(Request $request, string $key, Security $security): JsonResponse
    {   
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {

            try {

                // Check user credits first
                if ($user->getCreditPoints() <= 0) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Insufficient credits in your account. Please buy more credits and try again.'
                    ], 400); // 400 Bad Request
                }


                // Load the builder
                $builder = DealDeskBuilder::getByPath("/DealDeskbuilders/$key");
                
                if (!$builder) {
                    throw $this->createNotFoundException('Builder not found');
                }

                // Get the selected slot data from request
                $slotNumber = $request->request->get('slotNumber');
                $userName = $user->getfirstname() . ' ' . $user->getlastname();
                $userEmail = $user->getemail();

                // Decode current time slots
                $timeSlots = json_decode($builder->getTimeSlots(), true);

                // Update the selected slot
                foreach ($timeSlots['time_slots'] as &$slot) {
                    if ($slot['s_no'] == $slotNumber) {
                        $slot['availability'] = false;
                        $slot['bookedby_name'] = $userName;
                        $slot['bookedby_email'] = $userEmail;
                        break;
                    }
                }

                // Save the updated time slots
                $builder->setTimeSlots(json_encode($timeSlots));
                $builder->save();

                $user->setCreditPoints($user->getCreditPoints() - 1); // Deduct 1 credit point
                $user->save();

                // Send confirmation email
                $email = $user->getemail();
                $subject = "Meeting Slot Confirmed with " . $builder->getname();
                $htmlContent = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #2c3e50;">Your Meeting is Confirmed</h2>
                    <p>Hello ' . htmlspecialchars($userName) . ',</p>
                    
                    <p>You have successfully booked a meeting slot with <strong>' . htmlspecialchars($builder->getname()) . '.</p>
                    
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <p><strong>Date:</strong> 26th July, 2026</p>
                        <p><strong>Time Slot:</strong> ' . htmlspecialchars($selectedSlot['time_slot']) . '</p>
                        <p><strong>Location:</strong> ' . htmlspecialchars($builder->getLocation()) . '</p>
                    </div>
                    
                    <p>Please arrive 5-10 minutes before your scheduled time.</p>
                    
                    <p style="margin-top: 30px;">Best regards,<br>
                    <strong>ArQonZ Global Ltd</strong><br>
                    <a href="https://arqonz.com" style="color: #3498db;">arqonz.com</a></p>
                </div>';

                $this->sendEmail($email, $subject, $htmlContent);


                return new JsonResponse([
                    'success' => true,
                    'message' => 'Slot booked successfully!'
                ]);

            } catch (\Exception $e) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Error booking slot: ' . $e->getMessage()
                ], 500);
            }
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'Authentication required'
        ], 401);

    }


    /**
     * @Route("/deal-desk/cancel-slot/{key}", name="deal_desk_cancel_slot", methods={"POST"})
     */
    public function cancelSlotAction(Request $request, string $key, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            try {
                // Load the builder
                $builder = DealDeskBuilder::getByPath("/DealDeskbuilders/$key");
                
                if (!$builder) {
                    throw $this->createNotFoundException('Builder not found');
                }

                // Get the selected slot data from request
                $slotNumber = $request->request->get('slotNumber');

                // Decode current time slots
                $timeSlots = json_decode($builder->getTimeSlots(), true);

                // Update the selected slot
                foreach ($timeSlots['time_slots'] as &$slot) {
                    if ($slot['s_no'] == $slotNumber && $slot['bookedby_email'] === $user->getEmail()) {
                        $slot['availability'] = true;
                        $slot['bookedby_name'] = null;
                        $slot['bookedby_email'] = null;
                        break;
                    }
                }

                // Save the updated time slots
                $builder->setTimeSlots(json_encode($timeSlots));
                $builder->save();

                // Send cancellation email
                $email = $user->getemail();
                $subject = "Meeting Cancelled with " . $builder->getname();
                $htmlContent = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #2c3e50;">Meeting Cancellation Confirmation</h2>
                    <p>Hello ' . htmlspecialchars($userName) . ',</p>
                    
                    <p>Your meeting slot with <strong>' . htmlspecialchars($builder->getname()) . '</strong> has been cancelled.</p>
                    
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <p><strong>Cancelled Slot:</strong> ' . htmlspecialchars($cancelledSlot['time_slot']) . '</p>
                    </div>
                    
                    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0;">
                        <p><strong>Note:</strong> Credit points used for booking are non-refundable.</p>
                    </div>
                    
                    <p style="margin-top: 30px;">Best regards,<br>
                    <strong>ArQonZ Global Ltd</strong><br>
                    <a href="https://arqonz.com" style="color: #3498db;">arqonz.com</a></p>
                </div>';

                $this->sendEmail($email, $subject, $htmlContent);

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Slot cancelled successfully!'
                ]);



            } catch (\Exception $e) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Error cancelling slot: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return new JsonResponse([
            'success' => false,
            'message' => 'Authentication required'
        ], 401);
    }


    /**
     * @Route("/deal-desk/reschedule-slot/{key}", name="deal_desk_reschedule_slot", methods={"POST"})
     */
    public function rescheduleSlotAction(Request $request, string $key, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            try {
                // Load the builder
                $builder = DealDeskBuilder::getByPath("/DealDeskbuilders/$key");
                
                if (!$builder) {
                    throw $this->createNotFoundException('Builder not found');
                }

                // Get the selected slots data from request
                $oldSlotNumber = $request->request->get('oldSlotNumber');
                $newSlotNumber = $request->request->get('newSlotNumber');
                $userName = $user->getfirstname() . ' ' . $user->getlastname();
                $userEmail = $user->getemail();

                // Decode current time slots
                $timeSlots = json_decode($builder->getTimeSlots(), true);

                // First, free up the old slot
                foreach ($timeSlots['time_slots'] as &$slot) {
                    if ($slot['s_no'] == $oldSlotNumber && $slot['bookedby_email'] === $userEmail) {
                        $slot['availability'] = true;
                        $slot['bookedby_name'] = null;
                        $slot['bookedby_email'] = null;
                        break;
                    }
                }

                // Then, book the new slot
                foreach ($timeSlots['time_slots'] as &$slot) {
                    if ($slot['s_no'] == $newSlotNumber && $slot['availability'] === true) {
                        $slot['availability'] = false;
                        $slot['bookedby_name'] = $userName;
                        $slot['bookedby_email'] = $userEmail;
                        break;
                    }
                }

                // Save the updated time slots
                $builder->setTimeSlots(json_encode($timeSlots));
                $builder->save();

                // Send rescheduling email
                $email = $user->getemail();
                $subject = "Meeting Rescheduled with " . $builder->getname();
                $htmlContent = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #2c3e50;">Your Meeting Has Been Rescheduled</h2>
                    <p>Hello ' . htmlspecialchars($userName) . ',</p>
                    
                    <p>Your meeting with <strong>' . htmlspecialchars($builder->getname()) . '</strong> has been rescheduled.</p>
                    
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <p><strong>Previous Time:</strong> ' . htmlspecialchars($oldSlot['time_slot']) . '</p>
                        <p><strong>New Time:</strong> ' . htmlspecialchars($newSlot['time_slot']) . '</p>
        
                        <p><strong>Date:</strong> 26th July, 2026</p>
                    </div>
                    
                    <p>Please make note of your new meeting time.</p>
                    
                    <p style="margin-top: 30px;">Best regards,<br>
                    <strong>ArQonZ Global Ltd</strong><br>
                    <a href="https://arqonz.com" style="color: #3498db;">arqonz.com</a></p>
                </div>';

                $this->sendEmail($email, $subject, $htmlContent);

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Slot rescheduled successfully!'
                ]);

            } catch (\Exception $e) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Error rescheduling slot: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return new JsonResponse([
            'success' => false,
            'message' => 'Authentication required'
        ], 401);
    }



    /**
     * @Route("/api/arqonz-chat/{customerId}", name="api_arqonz_chat", methods={"GET"})
     */
    public function apiArqonzChat($customerId, Request $request, LoggerInterface $logger): JsonResponse
    {   
        // Load the customer
        $customers = new \Pimcore\Model\DataObject\Customer\Listing();
        $customers->addConditionParam("UserID = ?", $customerId);
        $customersList = $customers->load();

        if (empty($customersList)) {
            $logger->warning("No customer found with UserID: {$customerId}");
            return new JsonResponse([
                'success' => false,
                'message' => 'Customer not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $customer = $customersList[0];
        
        // Get all threads for this customer
        $AqThreads = $customer->getAqThread();
        
        // Prepare thread data for JSON response
        $threadsData = [];
        foreach ($AqThreads as $thread) {
            $conversationHistory = json_decode($thread->getConversationHistory(), true) ?? [];
            
            $threadsData[] = [
                'ThreadId' => $thread->getThreadId(),
                'ThreadTitle' => $thread->getThreadTitle(),
                'conversationHistory' => $conversationHistory
            ];
        }
        
        // Reverse to show newest first
        $threadsData = array_reverse($threadsData);

        return new JsonResponse([
            'success' => true,
            'threads' => $threadsData
        ]);
    }



    /**
     * @Route("/api/create-thread/{customerId}", name="api_create_thread", methods={"POST"})
     */
    public function apiCreateThread($customerId, LoggerInterface $logger, Security $security): JsonResponse
    {   
        // Load the customer
        $customers = new \Pimcore\Model\DataObject\Customer\Listing();
        $customers->addConditionParam("UserID = ?", $customerId);
        $customersList = $customers->load();
        
        if (empty($customersList)) {
            return new JsonResponse(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $customer = $customersList[0];
        
        $openAIConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
        $apiKey = $openAIConfig['api_key'];
        $apiUrl = $openAIConfig['api_url'];
        
        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key not configured');
        }
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                    'OpenAI-Beta'   => 'assistants=v2',
                ],
                'json' => new \stdClass(),
            ]);

            $responseData = json_decode($response->getBody(), true);
            $threadId = $responseData['id'] ?? null;

            if ($threadId) {
                // Create a new AqThread object
                $aqThread = new \Pimcore\Model\DataObject\AqThread();
                $aqThread->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/AQIQ-Threads'));
                $aqThread->setThreadId($threadId);
                $aqThread->setCustomer($customer);
                $aqThread->setConversationHistory('[]');
                $aqThread->setThreadTitle('New Chat');
                $aqThread->setKey($threadId);
                $aqThread->setPublished(true);
                $aqThread->save();

                return new JsonResponse([
                    'success' => true,
                    'threadId' => $threadId
                ]);
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Thread creation failed'], 500);
            }
        } catch (\Exception $e) {
            $logger->error('Error creating thread: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }




    

    /**
     * @Route("/api/create-thread/{customerId}", name="mobile_create_thread", methods={"POST"})
     */
    public function mobilecreateThreadAPI($customerId, LoggerInterface $logger, Security $security): JsonResponse
    {   
        
    
        // Load the customer
        $customers = new \Pimcore\Model\DataObject\Customer\Listing();
        $customers->addConditionParam("UserID = ?", $customerId);
        $customersList = $customers->load();

        if (empty($customersList)) {
            $logger->warning("No customer found with UserID: {$customerId}");
            return new JsonResponse([
                'success' => false,
                'message' => 'Customer not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $customer = $customersList[0];
        // $logger->info("Customer found: " . $customer->getKey());

        // Load the ProProfile associated with the customer
        $proProfiles = $customer->getPortfolio();
        if (empty($proProfiles)) {
            // $logger->warning("No portfolio found for Customer ID: {$customerID}");
            return new JsonResponse([
                'success' => false,
                'message' => 'ProProfile not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $proProfile = $proProfiles[0];


        $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
        $apiKey = $openaiConfig['api_key'];
        $apiUrl = 'https://api.openai.com/v1/threads';
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                    'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                ],
                'json' => new \stdClass(), // Empty JSON body
            ]);

            $responseData = json_decode($response->getBody(), true);
            $threadId = $responseData['id'] ?? null;

            if ($threadId) {
                // Return a JSON response with the thread ID


                return new JsonResponse([
                    'success' => true,
                    'threadId' => $threadId
                ]);
            } else {
                // Handle the case where the thread creation failed
                return new JsonResponse(['success' => false, 'message' => 'Thread creation failed'], 500);
            }
        } catch (\Exception $e) {
            // Log the error and return a failure response
            $logger->error('Error creating thread: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }




    /**
     * @Route("/api/arqonz-thread-chat/{threadid}", name="API_Arqonz-Chat-Thread")
     */
    public function APIArqonzChatThread($threadid, Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {


        $threadId = $threadid;

        $AQThreadsList = new \Pimcore\Model\DataObject\AqThread\Listing();
        $AQThreadsList->addConditionParam("ThreadId = ?", $threadId);
        $AQThreads = $AQThreadsList->load();
        
        
        
        if (empty($AQThreads)) {
            $conversationHistory = null;
        }
        else {
            $AQThread = $AQThreads[0];
            $conversationHistoryJson = $AQThread->getConversationHistory();
            $conversationHistory = json_decode($conversationHistoryJson, true); 


            
            return new JsonResponse([
                'success' => true,
                'threadId' => $threadId,
                'conversationHistory' => $conversationHistory,
            ]);
        }

        // If JSON request (from mobile app), return JSON response
    
        
        return new JsonResponse([
            'success' => true,
            'threadId' => $threadId,
            'conversationHistory' => null,
        ]);
        
    }


    /**
     * @Route("/api/arqonz-ai-send/{customerId}", name="Api_arqonz_ai_send", methods={"POST"})
     */
    public function ApisendMessage($customerId, Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {   
        // Get the request content
        $content = json_decode($request->getContent(), true);

        // Load the customer
        $customers = new \Pimcore\Model\DataObject\Customer\Listing();
        $customers->addConditionParam("UserID = ?", $customerId);
        $customersList = $customers->load();

        if (empty($customersList)) {
            $logger->warning("No customer found with UserID: {$customerId}");
            return new JsonResponse([
                'success' => false,
                'message' => 'Customer not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $customer = $customersList[0];


        $userMessage = $content['message'] ?? null;
        $threadId = $content['threadId'] ?? null;
        
        $logger->info('threadId: ' . $threadId);
        $logger->info('userMessage: ' . $userMessage);

        $AQThreadsList = new \Pimcore\Model\DataObject\AqThread\Listing();
        $AQThreadsList->addConditionParam("ThreadId = ?", $threadId);
        $AQThreads = $AQThreadsList->load();
        
        
        if (empty($AQThreads)) {
            $AQThread = new AqThread();
            $AQThread->setParent(Service::createFolderByPath('/AQIQ-Threads'));
            $AQThread->setThreadId($threadId);
            $AQThread->setcustomer($customer);
            $AQThread->setConversationHistory('[]');
            $AQThread->setKey($threadId);

            if (strlen($userMessage) > 20) {
                $threadTitle = substr($userMessage, 0, 20) . '...';
            } else {
                $threadTitle = $userMessage;
            }

            $AQThread->setThreadTitle($threadTitle);
            $AQThread->setPublished(true);
            $AQThread->save();
        }
        else {
            $AQThread = $AQThreads[0];
        }

        $conversationHistoryJson = $AQThread->getConversationHistory();

        $conversationHistory = json_decode($conversationHistoryJson, true);            


        try {
            $messageId = null;

            $logger->info('threadId: ' . $threadId);

            $openaiConfig = \App\Service\EnvironmentConfigService::getOpenAIConfig();
        $apiKey = $openaiConfig['api_key'];
            $apiUrl = 'https://api.openai.com/v1/threads/' . $threadId . '/messages';
            $client = new Client();

            try {
                $response = $client->post($apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                        'OpenAI-Beta'   => 'assistants=v2',  // Required Beta header
                    ],
                    'json' => [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $userMessage,
                            ]
                        ],
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);

                $logger->info('Message added response: ' . json_encode($responseData));
                $messageId = $responseData['id'] ?? null;

            } catch (\Exception $e) {
                $logger->error('Error adding message to thread: ' . $e->getMessage());
                $messageId = null;
            }

            if (!$messageId) {
                $logger->error('Failed to add message to thread.');
                $this->addFlash('error', 'Could not add message to thread.');
                return $this->redirectToRoute('chatgpt');
            }
            $logger->info('Message added to thread. Message ID: ' . $messageId);

            $assistantResponse = $this->runThread($threadId, $logger);
            $assistantResponse = preg_replace('/【4:[^】]+】/u', '', $assistantResponse);
            if (!$assistantResponse) {
                $assistantResponse = 'Sorry, I could not get a response from the assistant.';
                $logger->error('Failed to get a response from assistant.');
            } else {
                // $parsedown = new Parsedown();
                // $assistantResponseHtml = $parsedown->text($assistantResponse);
                $AqMessage = new AqMessage();
                $AqMessage->setParent(Service::createFolderByPath('/AQIQ-Threads/Messages'));
                $AqMessage->setMessageId($messageId);
                $AqMessage->setAqThread($AQThread);
                $AqMessage->setUserMessage($userMessage);
                // $AqMessage->setBotReply($assistantResponseHtml);
                $AqMessage->setBotReply($assistantResponse);
                $AqMessage->setKey($messageId);
                $AqMessage->setPublished(true);
                $AqMessage->save();

                $logger->info('Assistant Response: ' . $assistantResponse);
            }

            // Update conversation history
            $conversationHistory[] = [
                'user' => $userMessage,
                'bot' => $assistantResponse,
            ];

            $updatedConversationHistoryJson = json_encode($conversationHistory);
            $AQThread->setConversationHistory($updatedConversationHistoryJson);
            $AQThread->save();

            // Return the response
            return new JsonResponse(['success' => true, 'botResponse' => $assistantResponse]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
        
        
    }




    /**
     * @Route("/mobile-create-order", name="mobile_create_order", methods={"POST"})
     */
    public function MobilecreateOrder(Request $request, Security $security)
    {
        // $user = $security->getUser();
        // if (!$user || !$this->isGranted('ROLE_USER')) {
        //     return $this->json(['error' => 'User not authenticated'], 401);
        // }

        $requestData = json_decode($request->getContent(), true);
        $plan = $requestData['plan'] ?? null;
        $includeAnnualFee = $requestData['includeAnnualFee'] ?? null;
        // $plan = $request->request->get('plan');
        $amount = 0;

        switch ($plan) {
            case 'Standard':
                $amount = 50000;
                break;
            case 'Silver':
                $amount = 150000;
                break;
            case 'Gold':
                $amount = 300000;
                break;
            case 'Platinum':
                $amount = 600000;
                // $amount = 100;
                break;
            default:
                return $this->json(['error' => 'Invalid plan selected'], 400);
        }

        // Add annual fee if applicable
        if ($includeAnnualFee == "true") {
            $amount += 50000; // 500 Rs in paise
            // $amount += 100;

        }

        // Apply 18% GST to the total amount
        $gstAmount = $amount * 0.18;
        $totalAmount = $amount + $gstAmount;
        $totalAmount = round($totalAmount); // Ensure we have a whole number for the payment gateway


        $razorpayKey = 'rzp_live_lHiuTO7zDrXx97'; 
        $razorpaySecret = 'pnZHy4LlAVFM1DgRyZiMfBOg'; 
        $api = new Api($razorpayKey, $razorpaySecret);

        

        try {
            $orderData = [
                'amount' => $totalAmount, // Amount in paisa
                'currency' => 'INR',
                'receipt' => 'order_' . time(),
                'payment_capture' => 1
            ];
            $razorpayOrder = $api->order->create($orderData);
            
            // Description should indicate if annual fee is included
            $description = "Payment for $plan Plan";
            if ($includeAnnualFee == "true") {
                // $description .= " with Annual Subscription Fee";
                $description .= " with Annual Subscription Fee";
            }
            $description .= " (Incl. 18% GST)";

            // Construct hosted checkout URL
            $paymentUrl = "https://api.razorpay.com/v1/checkout/embedded?order_id=" . $razorpayOrder['id'];

            return $this->json([
                'orderId' => $razorpayOrder['id'],
                'amount' => $amount,
                'razorpayKey' => $razorpayKey,
                'paymentUrl' => $paymentUrl,
                'name' => 'Arqonz Global Pvt. Ltd.',
                'description' => "Payment for $plan Plan",
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Razorpay order creation failed'], 500);
        }
    }



    /**
     * @Route("/api/customer-credits/{customerID}", name="customer-credits-api", methods={"GET"})
     */
    public function customerCreditsApiAction($customerID, Request $request): JsonResponse
    {
        try {
            $customer = \Pimcore\Model\DataObject\Customer::getByUserId($customerID, 1);
            
            if (!$customer) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'credits' => $customer->getCreditPoints() ?: 0,
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to fetch credits',
            ], 500);
        }
    }



    /**
     * @Route("/api/search", name="api_search")
     */
    public function ApisearchAction(Request $request)
    {
        // Get parameters from request
        $keyword = $request->query->get('keyword', '');
        $page = $request->query->get('page', 1);
        $itemsPerPage = $request->query->get('itemsPerPage', 10);
        
        // Validate parameters
        $page = max(1, intval($page));
        $itemsPerPage = max(1, intval($itemsPerPage));
        $keyword = strtolower(trim($keyword));

        // Get all ProProfile objects
        $proProfileList = new \Pimcore\Model\DataObject\ProProfile\Listing();
        // $proProfileList->setOrderKey('o_creationDate');
        $proProfileList->setOrder('DESC');
        
        // Apply filtering
        $totalProfiles = $proProfileList->load();
        
        // Filter results by keyword (similar to your web version)
        $filteredProfiles = array_filter($totalProfiles, function ($profile) use ($keyword) {
            // Adjust these conditions based on your requirements
            $portfolioType = strtolower($profile->getPortfolioType() ?? '');
            $companyName = strtolower($profile->getCompanyName() ?? '');
            $description = strtolower($profile->getDescription() ?? '');
            
            // "OR" condition: If the keyword is present in any of the fields, include the profile
            return strpos($portfolioType, $keyword) !== false ||
                   strpos($companyName, $keyword) !== false ||
                   strpos($description, $keyword) !== false;
        });
        
        // Calculate pagination
        $totalItems = count($filteredProfiles);
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        // Paginate results
        $offset = ($page - 1) * $itemsPerPage;
        $paginatedProfiles = array_slice($filteredProfiles, $offset, $itemsPerPage);
        
        // Format data for API response
        $formattedProfiles = [];
        foreach ($paginatedProfiles as $profile) {
            $profileData = [
                'id' => $profile->getId(),
                'Key' => $profile->getKey(),
                'companyName' => $profile->getCompanyName(),
                'description' => $profile->getDescription(),
                'yearEstablished' => $profile->getYearEstablished(),
                'projectsCount' => count($profile->getProjects() ?? []),
                'portfolioType' => $profile->getPortfolioType(),
                'profileImage' => null,
            ];
            
            // Get profile image if available
            $profileImage = $profile->getProfileImage();
            if ($profileImage instanceof \Pimcore\Model\Asset\Image) {
                $profileData['profileImage'] = $profileImage->getFullPath();
            }
            
            $formattedProfiles[] = $profileData;
        }
        
        // Prepare pagination data
        $paginationData = [
            'totalItems' => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];
        
        // Return JSON response
        return new JsonResponse([
            'success' => true,
            'data' => $formattedProfiles,
            'pagination' => $paginationData,
        ]);
    }



    












    /**
     * @Route("/dashboard-otp-verification", name="Dashboard-OTP-verification")
     */
    public function DashboardOTPVerification(Request $request, Security $security, LoggerInterface $logger): JsonResponse
    {
        $user = $security->getUser();
        $data = json_decode($request->getContent(), true);
        $enteredOTP = $data['EnteredOTP'] ?? null;
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $SystemOTP = $user->getMobileVerificationOTP();

            $logger->info('OTPs', [
                'entered' => $enteredOTP,
                'system' => $SystemOTP,
            ]);

            if ((int) $enteredOTP === (int) $SystemOTP) {
                $user->setPhoneVerified('True');
                $user->save();
                return new JsonResponse(['success' => true, 'message' => 'OTP Matched']);
            }
            else {
                return new JsonResponse(['success' => false, 'message' => 'OTP MisMatch']);
            }

        }

        return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }



    /**
     * @Route("/account/Products-Bulk-Upload", name="Products-Bulk-Upload")
     */
    public function ProductsBulkUpload(
        Security $security, 
        Request $request, 
        LoggerInterface $logger, 
        SessionInterface $session
    ) {
        $user = $security->getUser();
        $customer = $user;
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $form = $this->createForm(BulkProductsFormType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                // Get uploaded file
                $formData = $form->getData();
                $file = $formData['ProductsexcelFile'];
                $ProductUser = $formData['ManufacturerID'];
                $imageAssetPath = $formData['ImageAssetPath']; // New field for image asset path
                $ProProfile = ProProfile::getByPath("/Services/Manufacturers/Profiles/$ProductUser");
                
                // Define upload path
                $filePath = $file->getPathname();
                
                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                
                    // Validate if the file contains the expected format
                    if (count($rows) < 2) {
                        $this->addFlash('error', 'The uploaded Excel file contains no data.');
                        return $this->redirectToRoute('Products-Bulk-Upload');
                    }
                
                    // Process rows, skipping the header row
                    foreach ($rows as $index => $row) {
                        if ($index === 0) continue; // Skip header row
                        
                        // Skip empty rows
                        if (empty($row[0]) && empty($row[1])) continue;
                
                        $proProduct = new ProProduct();
                        $productName = $row[2] ?? ''; // Product Name column
                
                        // Skip if no product name
                        if (empty($productName)) continue;
                
                        // Generate key
                        $timestamp = time();
                        $randomDigits = rand(100, 999);
                        $key = strtolower(str_replace(' ', '-', $productName)) . '-' . $timestamp.$randomDigits;
                        $proProduct->setKey($key);
                
                        // Set path
                        $proProduct->setParent(DataObject::getByPath('/Services/Manufacturers/Products/'));
                
                        // Populate object fields
                        $proProduct->setProductBrand($row[1] ?? null);
                        $proProduct->setProductName($row[3] ?? null);
                        $proProduct->setProductDescription($row[4] ?? null);
                        $proProduct->setPrice((float)($row[5] ?? 0));
                        $proProduct->setUnit($row[6] ?? null);
                        $proProduct->setMaterial($row[7] ?? null);
                        
                        // Process image from column 8 (9th column - 0-indexed)
                        $imageName = $row[8] ?? null; // Image name from Excel
                        
                        $proProduct->setParentCategory($row[9] ?? null);
                        $proProduct->setSubCategory($row[10] ?? null);
                        $proProduct->setSubSubCategory($row[11] ?? null);
                        $proProduct->setTags($row[12] ?? null);
                        $proProduct->setSpecifications($row[13] ?? null);
                        $proProduct->setProfessional($ProProfile);

                        // Process image based on image name and asset path
                        if (!empty($imageName) && !empty($imageAssetPath)) {
                            $logger->info('Processing image for product: ' . $productName);
                            $logger->info('Image name from Excel: ' . $imageName);
                            $logger->info('Image asset path: ' . $imageAssetPath);
                            
                            // Ensure asset path starts and ends with forward slash
                            $normalizedPath = rtrim(ltrim($imageAssetPath, '/'), '/');
                            $fullAssetPath = '/' . $normalizedPath . '/' . $imageName;
                            
                            $logger->info('Full asset path: ' . $fullAssetPath);
                            
                            try {
                                // Try to get the existing asset
                                $existingAsset = \Pimcore\Model\Asset::getByPath($fullAssetPath);
                                
                                if ($existingAsset && $existingAsset instanceof \Pimcore\Model\Asset\Image) {
                                    $logger->info('Found existing asset: ' . $fullAssetPath);
                                    // Create ImageGallery with the single image
                                    $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                                    $hotspotImage->setImage($existingAsset);
                                    $imageGallery = new \Pimcore\Model\DataObject\Data\ImageGallery([$hotspotImage]);
                                    $proProduct->setProductImage($imageGallery);
                                } else {
                                    $logger->warning('Asset not found at path: ' . $fullAssetPath . ' for product: ' . $productName);
                                    // Optionally, you can add a flash message for missing images
                                    // $this->addFlash('warning', 'Image not found for product: ' . $productName . ' at path: ' . $fullAssetPath);
                                }
                            } catch (\Exception $e) {
                                $logger->error('Error processing image for product ' . $productName . ': ' . $e->getMessage());
                            }
                        } else {
                            if (empty($imageName)) {
                                $logger->info('No image name provided for product: ' . $productName);
                            }
                            if (empty($imageAssetPath)) {
                                $logger->info('No image asset path provided for product: ' . $productName);
                            }
                        }
                
                        // Save object
                        $proProduct->setPublished(true);
                        $proProduct->save();
                        $logger->info('Product saved successfully: ' . $proProduct->getKey());
                    }
                
                    $this->addFlash('success', 'Products have been successfully uploaded and created.');
                } catch (\Exception $e) {
                    $logger->error('Error uploading products: ' . $e->getMessage());
                    $this->addFlash('error', 'An error occurred while processing the file. Please check the file format and data.');
                }
                
                return $this->redirectToRoute('Products-Bulk-Upload');
            }
            
            return $this->render('/Professional/Dashboard/Products_upload_bulk.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
            ]);
        }
        
        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    
    /**
     * @Route("/account/distributor-Products-Bulk-Upload", name="Distributor-Products-Bulk-Upload")
     */
    public function distributorProductsBulkUpload(
        Security $security, 
        Request $request, 
        LoggerInterface $logger, 
        SessionInterface $session
    ) {
        $user = $security->getUser();
        $customer = $user;

        if ($user && $this->isGranted('ROLE_USER')) {
            $form = $this->createForm(BulkProductsFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Get uploaded file
                $formData = $form->getData();
                $file = $formData['ProductsexcelFile'];
                $ProductUser = $formData['ManufacturerID'];
                $imageAssetPath = $formData['ImageAssetPath']; // New field for image asset path
                $ProProfile = ProProfile::getByPath("/Services/Distributors/Profiles/$ProductUser");
                $logger->info('Image Asset Path: ' . $imageAssetPath);

                // Define upload path
                $filePath = $file->getPathname();

                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                
                    // Validate if the file contains the expected format
                    if (count($rows) < 2) {
                        $this->addFlash('error', 'The uploaded Excel file contains no data.');
                        return $this->redirectToRoute('Products-Bulk-Upload');
                    }
                
                    // Process rows, skipping the header row
                    foreach ($rows as $index => $row) {
                        if ($index === 0) continue; // Skip header row
                        
                        // Skip empty rows
                        if (empty($row[0]) && empty($row[1])) continue;
                
                        $proProduct = new ProProduct();
                        $productName = $row[2] ?? ''; // Product Name column
                
                        // Skip if no product name
                        if (empty($productName)) continue;
                
                        // Generate key
                        $timestamp = time();
                        $randomDigits = rand(100, 999);
                        $key = strtolower(str_replace(' ', '-', $productName)) . '-' . $timestamp.$randomDigits;
                        $proProduct->setKey($key);
                
                        // Set path
                        $proProduct->setParent(DataObject::getByPath('/Services/Distributors/Products/'));
                
                        // Populate object fields
                        $proProduct->setProductBrand($row[1] ?? null);
                        $proProduct->setProductName($row[3] ?? null);
                        $proProduct->setProductDescription($row[4] ?? null);
                        $proProduct->setPrice((float)($row[5] ?? 0));
                        $proProduct->setUnit($row[6] ?? null);
                        $proProduct->setMaterial($row[7] ?? null);
                        
                        // Process image from column 8 (9th column - 0-indexed)
                        $imageName = $row[8] ?? null; // Image name from Excel
                        $logger->info('Image name From Excel: ' . $imageName);
                        
                        $proProduct->setParentCategory($row[9] ?? null);
                        $proProduct->setSubCategory($row[10] ?? null);
                        $proProduct->setSubSubCategory($row[11] ?? null);
                        $proProduct->setTags($row[12] ?? null);
                        $proProduct->setSpecifications($row[13] ?? null);
                        $proProduct->setProfessional($ProProfile);

                        // Process image based on image name and asset path
                        if (!empty($imageName) && !empty($imageAssetPath)) {
                            $logger->info('Processing image for product: ' . $productName);
                            $logger->info('Image name from Excel: ' . $imageName);
                            $logger->info('Image asset path: ' . $imageAssetPath);
                            
                            // Ensure asset path starts and ends with forward slash
                            $normalizedPath = rtrim(ltrim($imageAssetPath, '/'), '/');
                            $fullAssetPath = '/' . $normalizedPath . '/' . $imageName;
                            
                            $logger->info('Full asset path: ' . $fullAssetPath);
                            
                            try {
                                // Try to get the existing asset
                                $existingAsset = \Pimcore\Model\Asset::getByPath($fullAssetPath);
                                
                                if ($existingAsset && $existingAsset instanceof \Pimcore\Model\Asset\Image) {
                                    $logger->info('Found existing asset: ' . $fullAssetPath);
                                    // Create ImageGallery with the single image
                                    $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                                    $hotspotImage->setImage($existingAsset);
                                    $imageGallery = new \Pimcore\Model\DataObject\Data\ImageGallery([$hotspotImage]);
                                    $proProduct->setProductImage($imageGallery);
                                } else {
                                    $logger->warning('Asset not found at path: ' . $fullAssetPath . ' for product: ' . $productName);
                                    // Optionally, you can add a flash message for missing images
                                    // $this->addFlash('warning', 'Image not found for product: ' . $productName . ' at path: ' . $fullAssetPath);
                                }
                            } catch (\Exception $e) {
                                $logger->error('Error processing image for product ' . $productName . ': ' . $e->getMessage());
                            }
                        } else {
                            if (empty($imageName)) {
                                $logger->info('No image name provided for product: ' . $productName);
                            }
                            if (empty($imageAssetPath)) {
                                $logger->info('No image asset path provided for product: ' . $productName);
                            }
                        }
                
                        // Save object
                        $proProduct->setPublished(true);
                        $proProduct->save();
                        $logger->info('Product saved successfully: ' . $proProduct->getKey());
                    }
                
                    $this->addFlash('success', 'Products have been successfully uploaded and created.');
                } catch (\Exception $e) {
                    $logger->error('Error uploading products: ' . $e->getMessage());
                    $this->addFlash('error', 'An error occurred while processing the file. Please check the file format and data.');
                }

                return $this->redirectToRoute('Distributor-Products-Bulk-Upload');
            }

            return $this->render('/Professional/Dashboard/Products_upload_bulk.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/account/retailer-Products-Bulk-Upload", name="Retailer-Products-Bulk-Upload")
     */
    public function RetailerProductsBulkUpload(
        Security $security, 
        Request $request, 
        LoggerInterface $logger, 
        SessionInterface $session
    ) {
        $user = $security->getUser();
        $customer = $user;

        if ($user && $this->isGranted('ROLE_USER')) {
            $form = $this->createForm(BulkProductsFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Get uploaded file
                $formData = $form->getData();
                $file = $formData['ProductsexcelFile'];
                $ProductUser = $formData['ManufacturerID'];
                $imageAssetPath = $formData['ImageAssetPath']; // New field for image asset path
                $ProProfile = ProProfile::getByPath("/Services/Retailers/Profiles/$ProductUser");
                $logger->info('Image Asset Path: ' . $imageAssetPath);

                // Define upload path
                $filePath = $file->getPathname();

                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                
                    // Validate if the file contains the expected format
                    if (count($rows) < 2) {
                        $this->addFlash('error', 'The uploaded Excel file contains no data.');
                        return $this->redirectToRoute('Products-Bulk-Upload');
                    }
                
                    // Process rows, skipping the header row
                    foreach ($rows as $index => $row) {
                        if ($index === 0) continue; // Skip header row
                        
                        // Skip empty rows
                        if (empty($row[0]) && empty($row[1])) continue;
                
                        $proProduct = new ProProduct();
                        $productName = $row[2] ?? ''; // Product Name column
                
                        // Skip if no product name
                        if (empty($productName)) continue;
                
                        // Generate key
                        $timestamp = time();
                        $randomDigits = rand(100, 999);
                        $key = strtolower(str_replace(' ', '-', $productName)) . '-' . $timestamp.$randomDigits;
                        $proProduct->setKey($key);
                
                        // Set path
                        $proProduct->setParent(DataObject::getByPath('/Services/Retailers/Products/'));
                
                        // Populate object fields
                        $proProduct->setProductBrand($row[1] ?? null);
                        $proProduct->setProductName($row[3] ?? null);
                        $proProduct->setProductDescription($row[4] ?? null);
                        $proProduct->setPrice((float)($row[5] ?? 0));
                        $proProduct->setUnit($row[6] ?? null);
                        $proProduct->setMaterial($row[7] ?? null);
                        
                        // Process image from column 8 (9th column - 0-indexed)
                        $imageName = $row[8] ?? null; // Image name from Excel
                        $logger->info('Image name From Excel: ' . $imageName);
                        
                        $proProduct->setParentCategory($row[9] ?? null);
                        $proProduct->setSubCategory($row[10] ?? null);
                        $proProduct->setSubSubCategory($row[11] ?? null);
                        $proProduct->setTags($row[12] ?? null);
                        $proProduct->setSpecifications($row[13] ?? null);
                        $proProduct->setProfessional($ProProfile);

                        // Process image based on image name and asset path
                        if (!empty($imageName) && !empty($imageAssetPath)) {
                            $logger->info('Processing image for product: ' . $productName);
                            $logger->info('Image name from Excel: ' . $imageName);
                            $logger->info('Image asset path: ' . $imageAssetPath);
                            
                            // Ensure asset path starts and ends with forward slash
                            $normalizedPath = rtrim(ltrim($imageAssetPath, '/'), '/');
                            $fullAssetPath = '/' . $normalizedPath . '/' . $imageName;
                            
                            $logger->info('Full asset path: ' . $fullAssetPath);
                            
                            try {
                                // Try to get the existing asset
                                $existingAsset = \Pimcore\Model\Asset::getByPath($fullAssetPath);
                                
                                if ($existingAsset && $existingAsset instanceof \Pimcore\Model\Asset\Image) {
                                    $logger->info('Found existing asset: ' . $fullAssetPath);
                                    // Create ImageGallery with the single image
                                    $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                                    $hotspotImage->setImage($existingAsset);
                                    $imageGallery = new \Pimcore\Model\DataObject\Data\ImageGallery([$hotspotImage]);
                                    $proProduct->setProductImage($imageGallery);
                                } else {
                                    $logger->warning('Asset not found at path: ' . $fullAssetPath . ' for product: ' . $productName);
                                    // Optionally, you can add a flash message for missing images
                                    // $this->addFlash('warning', 'Image not found for product: ' . $productName . ' at path: ' . $fullAssetPath);
                                }
                            } catch (\Exception $e) {
                                $logger->error('Error processing image for product ' . $productName . ': ' . $e->getMessage());
                            }
                        } else {
                            if (empty($imageName)) {
                                $logger->info('No image name provided for product: ' . $productName);
                            }
                            if (empty($imageAssetPath)) {
                                $logger->info('No image asset path provided for product: ' . $productName);
                            }
                        }
                
                        // Save object
                        $proProduct->setPublished(true);
                        $proProduct->save();
                        $logger->info('Product saved successfully: ' . $proProduct->getKey());
                    }
                
                    $this->addFlash('success', 'Products have been successfully uploaded and created.');
                } catch (\Exception $e) {
                    $logger->error('Error uploading products: ' . $e->getMessage());
                    $this->addFlash('error', 'An error occurred while processing the file. Please check the file format and data.');
                }

                return $this->redirectToRoute('Retailer-Products-Bulk-Upload');
            }

            return $this->render('/Professional/Dashboard/Products_upload_bulk.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }

    

    /**
     * @Route("/account/dealer-Products-Bulk-Upload", name="Dealer-Products-Bulk-Upload")
     */
    public function dealerProductsBulkUpload(
        Security $security, 
        Request $request, 
        LoggerInterface $logger, 
        SessionInterface $session
    ) {
        $user = $security->getUser();
        $customer = $user;
        
        if ($user && $this->isGranted('ROLE_USER')) {
            $form = $this->createForm(BulkProductsFormType::class);
            $form->handleRequest($request);
                                                                        
            if ($form->isSubmitted() && $form->isValid()) {
                // Get uploaded file
                $formData = $form->getData();
                $file = $formData['ProductsexcelFile'];
                $ProductUser = $formData['ManufacturerID'];
                $imageAssetPath = $formData['ImageAssetPath']; // New field for image asset path
                $ProProfile = ProProfile::getByPath("/Services/Dealers/Profiles/$ProductUser");
                
                // Define upload path
                $filePath = $file->getPathname();
                
                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                
                    // Validate if the file contains the expected format
                    if (count($rows) < 2) {
                        $this->addFlash('error', 'The uploaded Excel file contains no data.');
                        return $this->redirectToRoute('Dealer-Products-Bulk-Upload');
                    }
                
                    // Process rows, skipping the header row
                    foreach ($rows as $index => $row) {
                        if ($index === 0) continue; // Skip header row
                        
                        // Skip empty rows
                        if (empty($row[0]) && empty($row[1])) continue;
                
                        $proProduct = new ProProduct();
                        $productName = $row[2] ?? ''; // Product Name column
                
                        // Skip if no product name
                        if (empty($productName)) continue;
                
                        // Generate key
                        $timestamp = time();
                        $randomDigits = rand(100, 999);
                        $key = strtolower(str_replace(' ', '-', $productName)) . '-' . $timestamp.$randomDigits;
                        $proProduct->setKey($key);
                
                        // Set path
                        $proProduct->setParent(DataObject::getByPath('/Services/Dealers/Products/'));
                
                        // Populate object fields
                        $proProduct->setProductBrand($row[1] ?? null);
                        $proProduct->setProductName($row[3] ?? null);
                        $proProduct->setProductDescription($row[4] ?? null);
                        $proProduct->setPrice((float)($row[5] ?? 0));
                        $proProduct->setUnit($row[6] ?? null);
                        $proProduct->setMaterial($row[7] ?? null);
                        
                        // Process image from column 8 (9th column - 0-indexed)
                        $imageName = $row[8] ?? null; // Image name from Excel
                        
                        $proProduct->setParentCategory($row[9] ?? null);
                        $proProduct->setSubCategory($row[10] ?? null);
                        $proProduct->setSubSubCategory($row[11] ?? null);
                        $proProduct->setTags($row[12] ?? null);
                        $proProduct->setSpecifications($row[13] ?? null);
                        $proProduct->setProfessional($ProProfile);

                        // Process image based on image name and asset path
                        if (!empty($imageName) && !empty($imageAssetPath)) {
                            $logger->info('Processing image for product: ' . $productName);
                            $logger->info('Image name from Excel: ' . $imageName);
                            $logger->info('Image asset path: ' . $imageAssetPath);
                            
                            // Ensure asset path starts and ends with forward slash
                            $normalizedPath = rtrim(ltrim($imageAssetPath, '/'), '/');
                            $fullAssetPath = '/' . $normalizedPath . '/' . $imageName;
                            
                            $logger->info('Full asset path: ' . $fullAssetPath);
                            
                            try {
                                // Try to get the existing asset
                                $existingAsset = \Pimcore\Model\Asset::getByPath($fullAssetPath);
                                
                                if ($existingAsset && $existingAsset instanceof \Pimcore\Model\Asset\Image) {
                                    $logger->info('Found existing asset: ' . $fullAssetPath);
                                    // Create ImageGallery with the single image
                                    $hotspotImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                                    $hotspotImage->setImage($existingAsset);
                                    $imageGallery = new \Pimcore\Model\DataObject\Data\ImageGallery([$hotspotImage]);
                                    $proProduct->setProductImage($imageGallery);
                                } else {
                                    $logger->warning('Asset not found at path: ' . $fullAssetPath . ' for product: ' . $productName);
                                    // Optionally, you can add a flash message for missing images
                                    // $this->addFlash('warning', 'Image not found for product: ' . $productName . ' at path: ' . $fullAssetPath);
                                }
                            } catch (\Exception $e) {
                                $logger->error('Error processing image for product ' . $productName . ': ' . $e->getMessage());
                            }
                        } else {
                            if (empty($imageName)) {
                                $logger->info('No image name provided for product: ' . $productName);
                            }
                            if (empty($imageAssetPath)) {
                                $logger->info('No image asset path provided for product: ' . $productName);
                            }
                        }
                
                        // Save object
                        $proProduct->setPublished(true);
                        $proProduct->save();
                        $logger->info('Product saved successfully: ' . $proProduct->getKey());
                    }
                
                    $this->addFlash('success', 'Products have been successfully uploaded and created.');
                } catch (\Exception $e) {
                    $logger->error('Error uploading products: ' . $e->getMessage());
                    $this->addFlash('error', 'An error occurred while processing the file. Please check the file format and data.');
                }
                
                return $this->redirectToRoute('Dealer-Products-Bulk-Upload');
            }
            
            return $this->render('/Professional/Dashboard/Products_upload_bulk.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
            ]);
        }
        
        return $this->render('Architect/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/architects-registrations", name="Architects-LandingPage")
     */
    public function ArchitectslandingPage(Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {

        return $this->render('/Professional/architects_landing.html.twig', [

        ]);
        
    }

     /**
     * @Route("/suppliers-registration", name="Suppliers-LandingPage")
     */
    public function SupplierslandingPage(Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {

        return $this->render('/Professional/suppliers_landing.html.twig', [

        ]);
        
    }



    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $dir
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }


    /**
     * @Route("/Account-Verification-Step-1", name="Account-Verification-Step-1")
     */
    public function AccountVerificationEmailAction(Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            // Redirect to Step 2 if already verified
            if ($customer->getEmailVerified() === 'true') {
                return $this->redirectToRoute('Account-Verification-Step-2');
            }

            $otpResent = false;
            $verificationStatus = null;

            // Generate and send OTP
            if (!$customer->getEmailVerificationOTP() || $request->get('resend_otp')) {
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

                if ($request->query->get('resend_otp')) {
                    $session->set('otp_resent', true);
                    // Redirect to the same route without the query parameter
                    return $this->redirectToRoute('Account-Verification-Step-1');
                }
            }
            // Retrieve and clear the session flag for otpResent
            $otpResent = $session->get('otp_resent', false);
            $session->remove('otp_resent');

            // Form Handling
            $form = $this->createForm(EmailVerificationFormtype::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $enteredOtp = $form->get('EmailOTP')->getData();
                $systemOtp = $customer->getEmailVerificationOTP();

                if ((int) $enteredOtp === (int) $systemOtp) {
                    $customer->setEmailVerified('true');
                    $customer->setEmailVerificationOTP(null); // Clear OTP
                    $customer->save();
                    $verificationStatus = 'success';
                } else {
                    $verificationStatus = 'failure';
                }
            }

            // Render template
            return $this->render('account/details_verification.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
                'verificationStatus' => $verificationStatus,
                'otpResent' => $otpResent,
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }


    /**
     * @Route("/Account-Verification-Step-2", name="Account-Verification-Step-2")
     */
    public function AccountVerificationPhoneAction(Security $security, Request $request, LoggerInterface $logger, SessionInterface $session)
    {
        $user = $security->getUser();

        if ($user && $this->isGranted('ROLE_USER')) {
            $customer = $user;

            // Redirect to Step 2 if already verified
            if ($customer->getPhoneVerified() === 'True') {
                return $this->redirectToRoute('account-index');
            }

            $otpResent = false;
            $verificationStatus = null;

            // Generate and send OTP
            if (!$customer->getMobileVerificationOTP() || $request->get('resend_otp')) {
                $otp = random_int(100000, 999999);
                $customer->setMobileVerificationOTP($otp);
                $customer->save();

                $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
                $this->GupsendWhatsAppMessage($customer->getPhone(), $otp, $OTPtemplateID);
                
                if ($request->query->get('resend_otp')) {
                    $session->set('otp_resent', true);
                    // Redirect to the same route without the query parameter
                    return $this->redirectToRoute('Account-Verification-Step-2');
                }

                
            }

            // Retrieve and clear the session flag for otpResent
            $otpResent = $session->get('otp_resent', false);
            $session->remove('otp_resent');
            // Form Handling
            $form = $this->createForm(EmailVerificationFormtype::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $enteredOtp = $form->get('EmailOTP')->getData();
                $systemOtp = $customer->getMobileVerificationOTP();

                // Log the OTP values for debugging
                $logger->info('Entered OTP: ' . $enteredOtp);
                $logger->info('System OTP: ' . $systemOtp);

                if ((int) $enteredOtp === (int) $systemOtp) {
                    $logger->info('OTP Matched. Verification Successful.');
                    $customer->setPhoneVerified('True');
                    $customer->setMobileVerificationOTP(null); // Clear OTP
                    $customer->save();
                    $verificationStatus = 'success';
                } else {
                    $logger->info('OTP Mismatch. Verification Failed.');
                    $verificationStatus = 'failure';
                }
            }

            // Render template
            return $this->render('account/phone_verification.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer,
                'verificationStatus' => $verificationStatus,
                'otpResent' => $otpResent,
            ]);
        }

        return $this->render('Architect/NotLogged_signup.html.twig');
    }




    /**
     * @Route("/Account-Verification/{url}", name="Account-Verification-OTP")
     */
    public function AccountVerificationPhoneEmailAction(Security $security, $url, Request $request, LoggerInterface $logger, SessionInterface $session)
    {
        $Customerlists = new \Pimcore\Model\DataObject\Customer\Listing();
        $Customerlists->addConditionParam("UserID = ?", $url);
        $Customers = $Customerlists->load();
        $customer = $Customers[0];

       

        $EmailotpResent = false;
        $EmailverificationStatus = null;
        $PhoneotpResent = false;
        $PhoneverificationStatus = null;

        // Redirect to Step 2 if already verified
        if ($customer->getEmailVerified() === 'true') {
            $EmailVerifiedIni = true;
        }
        if ($customer->getPhoneVerified() === 'True') {
            $PhoneVerifiedIni = true;
        }
        
        

        // Generate and send OTP
        if (!$customer->getEmailVerificationOTP() || $request->get('Email_resend_otp')) {
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

            if ($request->query->get('Email_resend_otp')) {
                $session->set('email_otp_resent', true);
                // Redirect to the same route without the query parameter
                return $this->redirectToRoute('Account-Verification-OTP', ['url' => $url]);
            }
        }
        // Retrieve and clear the session flag for otpResent
        $EmailotpResent = $session->get('email_otp_resent', false);
        $session->remove('email_otp_resent');


        // Generate and send OTP
        if (!$customer->getMobileVerificationOTP() || $request->get('phone_resend_otp')) {
            $Phoneotp = random_int(100000, 999999);
            $customer->setMobileVerificationOTP($Phoneotp);
            $customer->save();

            // $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
            // $this->GupsendWhatsAppMessage($customer->getPhone(), $Phoneotp, $OTPtemplateID);
            
            $whatsAppMessage = "*{$Phoneotp}* is your verification code. For your security, do not share this code.";
            $this->sendWhatsAppMessage($customer->getPhone(), $whatsAppMessage);


            if ($request->query->get('phone_resend_otp')) {
                $session->set('phone_otp_resent', true);
                // Redirect to the same route without the query parameter
                return $this->redirectToRoute('Account-Verification-OTP', ['url' => $url]);
            }

            
        }

        // Retrieve and clear the session flag for otpResent
        $PhoneotpResent = $session->get('phone_otp_resent', false);
        $session->remove('phone_otp_resent');



        // Form Handling
        $form = $this->createForm(CombinedVerificationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $EmailenteredOtp = $form->get('EmailOTP')->getData();
            $EmailsystemOtp = $customer->getEmailVerificationOTP();

            if ((int) $EmailenteredOtp === (int) $EmailsystemOtp) {
                $customer->setEmailVerified('true');
                $customer->setEmailVerificationOTP(null); // Clear OTP
                $customer->save();
                $EmailverificationStatus = 'success';
            } else {
                $EmailverificationStatus = 'failure';
            }


            $PhoneenteredOtp = $form->get('PhoneOTP')->getData();
            $PhonesystemOtp = $customer->getMobileVerificationOTP();

            // Log the OTP values for debugging
            $logger->info('Entered OTP: ' . $PhoneenteredOtp);
            $logger->info('System OTP: ' . $PhonesystemOtp);

            if ((int) $PhoneenteredOtp === (int) $PhonesystemOtp) {
                $logger->info('OTP Matched. Verification Successful.');
                $customer->setPhoneVerified('True');
                $customer->setMobileVerificationOTP(null); // Clear OTP
                $customer->save();
                $PhoneverificationStatus = 'success';
            } else {
                $logger->info('OTP Mismatch. Verification Failed.');
                $PhoneverificationStatus = 'failure';
            }

        }

        // Render template
        return $this->render('account/combined_verification.html.twig', [
            'form' => $form->createView(),
            'customer' => $customer,
            'EmailverificationStatus' => $EmailverificationStatus,
            'PhoneverificationStatus' => $PhoneverificationStatus,
            'EmailotpResent' => $EmailotpResent,
            'PhoneotpResent' => $PhoneotpResent,
            'UserID' => $url,
        ]);
        
    }


    /**
     * @Route("/accountemailVerification/{url}", name="Account-Email-Verification")
     */
    public function AccountEmailVerificationAction($url, LoggerInterface $logger, SessionInterface $session, Request $request, Security $security, PaginatorInterface $paginator, MailerInterface $mailer)
    {
        $Customerlists = new \Pimcore\Model\DataObject\Customer\Listing();
        $Customerlists->addConditionParam("EmailVerificationToken = ?", $url);
        $Customers = $Customerlists->load();
        $customer = $Customers[0];
        $customertype = $customer->getcustomertype();
        $UserverificationToken = $customer->getEmailVerificationToken();
        $alreadyemailverified = false;
        $alreadyphoneverified = false;

        if ($customer->getEmailVerified() === 'true') {
            $alreadyemailverified = true;
        }
        if ($customer->getPhoneVerified() === 'True') {
            $alreadyphoneverified = true;
        }
        if ($UserverificationToken ===  $url) {
            $EmailVerificationstatus = 'success';
            $customer->setEmailVerified('true');
            $customer->save();
        }
        else {
            $EmailVerificationstatus = 'fail';
        }


        $otpResent = false;
        $verificationStatus = null;

        // Generate and send OTP
        if (!$customer->getMobileVerificationOTP() || $request->get('resend_otp')) {
            $otp = random_int(100000, 999999);
            $customer->setMobileVerificationOTP($otp);
            $customer->save();

            $OTPtemplateID = "2fb9a88d-847d-41a5-a5b2-9c12df3b82b6";
            $this->GupsendWhatsAppMessage($customer->getPhone(), $otp, $OTPtemplateID);
            
            

            if ($request->query->get('resend_otp')) {
                $session->set('otp_resent', true);
                $currentRoute = $request->attributes->get('_route');
                $currentRouteParams = $request->attributes->get('_route_params');
                // Redirect to the same route without the query parameter
                return $this->redirectToRoute($currentRoute, $currentRouteParams);
            }

            
        }

        // Retrieve and clear the session flag for otpResent
        $otpResent = $session->get('otp_resent', false);
        $session->remove('otp_resent');
        // Form Handling
        $form = $this->createForm(EmailVerificationFormtype::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enteredOtp = $form->get('EmailOTP')->getData();
            $systemOtp = $customer->getMobileVerificationOTP();

            // Log the OTP values for debugging
            $logger->info('Entered OTP: ' . $enteredOtp);
            $logger->info('System OTP: ' . $systemOtp);

            if ((int) $enteredOtp === (int) $systemOtp) {
                $logger->info('OTP Matched. Verification Successful.');
                $customer->setPhoneVerified('True');
                $customer->setMobileVerificationOTP(null); // Clear OTP
                $customer->save();
                $verificationStatus = 'success';
            } else {
                $logger->info('OTP Mismatch. Verification Failed.');
                $verificationStatus = 'failure';
            }
        }

        
        
        return $this->render('Professional/Email_Phone_verification_template.html.twig', [
            'Emailstatus' =>  $EmailVerificationstatus,
            'form' => $form->createView(),
            'customer' => $customer,
            'verificationStatus' => $verificationStatus,
            'otpResent' => $otpResent,
            'alreadyphoneverified' => $alreadyphoneverified,
            'alreadyemailverified' => $alreadyemailverified,
        ]);
    
    }



    /**
     * @Route("/global-architect-builders-awards", name="Global-architect-awards")
     */
    public function GlobalArchitectAwardsAction(LoggerInterface $logger, Translator $translator, SessionInterface $session, Request $request, Security $security, PaginatorInterface $paginator, MailerInterface $mailer)
    {   
        $form = $this->createForm(GlobalAwardsFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $GlobalAwards = new GlobalAwards();
            $GlobalAwards->setParent(Service::createFolderByPath('/GlobalAwardsObjects'));
            $GlobalAwards->setKey(Text::toUrl($formData['OrganizationName'] . '-' . time()));

            
            if (isset($formData['OrganizationName'])) {
                $GlobalAwards->setOrganizationName($formData['OrganizationName']);
            }
            if (isset($formData['OfficeAddress'])) {
                $GlobalAwards->setOfficeAddress($formData['OfficeAddress']);
            }
            if (isset($formData['stateProvince'])) {
                $GlobalAwards->setstateProvince($formData['stateProvince']);
            }
            if (isset($formData['Country'])) {
                $GlobalAwards->setOfficeCountry($formData['Country']);
            }
            if (isset($formData['zipCode'])) {
                $GlobalAwards->setzipCode($formData['zipCode']);
            }
            if (isset($formData['email'])) {
                $GlobalAwards->setemail($formData['email']);
            }
            if (isset($formData['whatsappNumber'])) {
                $GlobalAwards->setwhatsappNumber($formData['whatsappNumber']);
            }
            if (isset($formData['projectType'])) {
                $GlobalAwards->setprojectType($formData['projectType']);
            }
            if (isset($formData['registrationYear'])) {
                $GlobalAwards->setregistrationYear($formData['registrationYear']);
            }
            if (isset($formData['projectsDone'])) {
                $GlobalAwards->setprojectsDone($formData['projectsDone']);
            }
            if (isset($formData['applicantName'])) {
                $GlobalAwards->setapplicantName($formData['applicantName']);
            }
            if (isset($formData['designation'])) {
                $GlobalAwards->setdesignation($formData['designation']);
            }
            if (isset($formData['age'])) {
                $GlobalAwards->setage($formData['age']);
            }
            if (isset($formData['experience'])) {
                $GlobalAwards->setexperience($formData['experience']);
            }
            if (isset($formData['contactNumber'])) {
                $GlobalAwards->setcontactNumber($formData['contactNumber']);
            }
            if (isset($formData['achievements'])) {
                $GlobalAwards->setachievements($formData['achievements']);
            }
            if (isset($formData['nominatingProject'])) {
                $GlobalAwards->setnominatingProject($formData['nominatingProject']);
            }
            if (isset($formData['projectLocation'])) {
                $GlobalAwards->setprojectLocation($formData['projectLocation']);
            }
            if (isset($formData['ProjectCountry'])) {
                $GlobalAwards->setprojectCountry($formData['ProjectCountry']);
            }
            if (isset($formData['projectCategory'])) {
                $GlobalAwards->setprojectCategory($formData['projectCategory']);
            }
            if (isset($formData['projectStatus'])) {
                $GlobalAwards->setprojectStatus($formData['projectStatus']);
            }
            if (isset($formData['projectArea'])) {
                $GlobalAwards->setprojectArea($formData['projectArea']);
            }
            if (isset($formData['website'])) {
                $GlobalAwards->setwebsite($formData['website']);
            }
            if (isset($formData['keyFeatures'])) {
                $GlobalAwards->setkeyFeatures($formData['keyFeatures']);
            }
            if (isset($formData['walkthroughLink'])) {
                $GlobalAwards->setwalkthroughLink($formData['walkthroughLink']);
            }
            $GlobalAwards->setPublished(true);
            $GlobalAwards->save();


            $EmailTemplates = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates->addConditionParam("TemplateName = ?", "GlobalAwardsConfirmation");
            $EmailTemplate = $EmailTemplates->load();
            $EmailTemplate = $EmailTemplate[0];

            $subject = $EmailTemplate->getSubject();
            $htmlContent = $EmailTemplate->getContent();
            
            $htmlContent = str_replace("[Recipient Name]", $form->get('OrganizationName')->getData(), $htmlContent);
            // Create a new Pimcore\Mail instance
            $mail = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail->to($form->get('email')->getData());
            $mail->subject($subject);
            $mail->html($htmlContent);
            $mail->send();


            $EmailTemplates1 = new \Pimcore\Model\DataObject\EmailTemplate\Listing();
            $EmailTemplates1->addConditionParam("TemplateName = ?", "GlobalAwardsConfirmation");
            $EmailTemplate1 = $EmailTemplates1->load();
            $EmailTemplate1 = $EmailTemplate1[0];

            $subject1 = $EmailTemplate1->getSubject();
            $htmlContent1 = $EmailTemplate1->getContent();
            
            $htmlContent1 = str_replace("[Recipient Name]", $form->get('OrganizationName')->getData(), $htmlContent1);
            // Create a new Pimcore\Mail instance
            $mail1 = new \Pimcore\Mail();
            // $mail->from('arqonztest@gmail.com');
            $mail1->getHeaders()->addMailboxListHeader('From', ['Arqonz Support <arqonztest@gmail.com>']);
            $mail1->to('support@arqonz.com');
            $mail1->subject($subject1);
            $mail1->html($htmlContent1);
            $mail1->send();












            $this->addFlash('success', $translator->trans('Registered Successfully.'));
            // return $this->redirectToRoute('account-index');
            return new Response('<script>window.location.href="https://www.globalarchitectbuilderawards.com/";</script>');
        }

        
        
        return $this->render('Professional/Global-architect-awards.html.twig', [
            'form' => $form->createView(),
        ]);
    
    }


    /**
     * @Route("/what-is-mycountry", name="What-is-mycountry")
     */
    public function whatIsMyCountry(Request $request, LoggerInterface $logger)
    {
        // Get client IP address
        $clientIP = $this->getClientIP($request);
        
        // Initialize location data
        $locationData = [
            'ip' => $clientIP,
            'country' => 'Unknown',
            'city' => 'Unknown',
            'countryCode' => '',
            'region' => '',
            'timezone' => '',
            'isp' => '',
            'error' => null
        ];

        try {
            // Try ip-api.com first (free, no API key required)
            $locationData = $this->getLocationFromIpApi($clientIP, $logger);
            
            // If ip-api fails, try ipapi.co as fallback
            if ($locationData['country'] === 'Unknown') {
                $locationData = $this->getLocationFromIpApiCo($clientIP, $logger);
            }
            
        } catch (\Exception $e) {
            $logger->error('Error getting location data: ' . $e->getMessage());
            $locationData['error'] = 'Unable to detect location. Please try again later.';
        }

        return $this->render('Professional/what_is_mycountry.html.twig', [
            'locationData' => $locationData
        ]);
    }

    /**
     * Get client IP address, considering proxies and load balancers
     */
    private function getClientIP(Request $request): string
    {
        // Check for IP from various headers in order of preference
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            $ip = $request->server->get($header);
            
            if (!empty($ip) && $ip !== 'unknown') {
                // Handle comma-separated list of IPs
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR
        return $request->getClientIp() ?: '127.0.0.1';
    }

    /**
     * Get location data from ip-api.com (free service)
     */
    private function getLocationFromIpApi(string $ip, LoggerInterface $logger): array
    {
        $locationData = [
            'ip' => $ip,
            'country' => 'Unknown',
            'city' => 'Unknown',
            'countryCode' => '',
            'region' => '',
            'timezone' => '',
            'isp' => '',
            'error' => null
        ];

        try {
            // ip-api.com endpoint with specific fields
            $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,timezone,isp,query";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; PimcoreLocationDetector/1.0)'
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                throw new \Exception('Failed to fetch data from ip-api.com');
            }

            $data = json_decode($response, true);
            
            if ($data && $data['status'] === 'success') {
                $locationData['country'] = $data['country'] ?? 'Unknown';
                $locationData['city'] = $data['city'] ?? 'Unknown';
                $locationData['countryCode'] = $data['countryCode'] ?? '';
                $locationData['region'] = $data['regionName'] ?? '';
                $locationData['timezone'] = $data['timezone'] ?? '';
                $locationData['isp'] = $data['isp'] ?? '';
                
                $logger->info('Successfully retrieved location from ip-api.com', [
                    'ip' => $ip,
                    'country' => $locationData['country'],
                    'city' => $locationData['city']
                ]);
            } else {
                $error = $data['message'] ?? 'Unknown error from ip-api.com';
                $logger->warning('ip-api.com returned error: ' . $error);
                $locationData['error'] = $error;
            }
            
        } catch (\Exception $e) {
            $logger->error('Error with ip-api.com: ' . $e->getMessage());
            $locationData['error'] = $e->getMessage();
        }

        return $locationData;
    }

    /**
     * Get location data from ipapi.co (fallback service)
     */
    private function getLocationFromIpApiCo(string $ip, LoggerInterface $logger): array
    {
        $locationData = [
            'ip' => $ip,
            'country' => 'Unknown',
            'city' => 'Unknown',
            'countryCode' => '',
            'region' => '',
            'timezone' => '',
            'isp' => '',
            'error' => null
        ];

        try {
            // ipapi.co endpoint
            $url = "https://ipapi.co/{$ip}/json/";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; PimcoreLocationDetector/1.0)'
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                throw new \Exception('Failed to fetch data from ipapi.co');
            }

            $data = json_decode($response, true);
            
            if ($data && !isset($data['error'])) {
                $locationData['country'] = $data['country_name'] ?? 'Unknown';
                $locationData['city'] = $data['city'] ?? 'Unknown';
                $locationData['countryCode'] = $data['country_code'] ?? '';
                $locationData['region'] = $data['region'] ?? '';
                $locationData['timezone'] = $data['timezone'] ?? '';
                $locationData['isp'] = $data['org'] ?? '';
                
                $logger->info('Successfully retrieved location from ipapi.co', [
                    'ip' => $ip,
                    'country' => $locationData['country'],
                    'city' => $locationData['city']
                ]);
            } else {
                $error = $data['reason'] ?? 'Unknown error from ipapi.co';
                $logger->warning('ipapi.co returned error: ' . $error);
                $locationData['error'] = $error;
            }
            
        } catch (\Exception $e) {
            $logger->error('Error with ipapi.co: ' . $e->getMessage());
            $locationData['error'] = $e->getMessage();
        }

        return $locationData;
    }

    














    


    


} //end Bracket