<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ===== Base Interfaces =====
use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Interfaces\Services\BaseServiceInterface;

// ===== Language =====
use App\Interfaces\Repositories\Admin\LanguageRepositoryInterface;
use App\Repositories\Admin\LanguageRepository;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Services\Admin\LanguageService;

// ===== About =====
use App\Interfaces\Repositories\Admin\AboutRepositoryInterface;
use App\Repositories\Admin\AboutRepository;
use App\Interfaces\Services\Admin\AboutServiceInterface;
use App\Services\Admin\AboutService;

// ===== Action =====
use App\Interfaces\Repositories\Admin\ActionRepositoryInterface;
use App\Repositories\Admin\ActionRepository;
use App\Interfaces\Services\Admin\ActionServiceInterface;
use App\Services\Admin\ActionService;

// ===== Address =====
use App\Interfaces\Repositories\Admin\AddressRepositoryInterface;
use App\Repositories\Admin\AddressRepository;
use App\Interfaces\Services\Admin\AddressServiceInterface;
use App\Services\Admin\AddressService;

// ===== AddressType =====
use App\Interfaces\Repositories\Admin\AddressTypeRepositoryInterface;
use App\Repositories\Admin\AddressTypeRepository;
use App\Interfaces\Services\Admin\AddressTypeServiceInterface;
use App\Services\Admin\AddressTypeService;

// ===== Administration =====
use App\Interfaces\Repositories\Admin\AdministrationRepositoryInterface;
use App\Repositories\Admin\AdministrationRepository;
use App\Interfaces\Services\Admin\AdministrationServiceInterface;
use App\Services\Admin\AdministrationService;

// ===== BlogCategory =====
use App\Interfaces\Repositories\Admin\BlogCategoryRepositoryInterface;
use App\Repositories\Admin\BlogCategoryRepository;
use App\Interfaces\Services\Admin\BlogCategoryServiceInterface;
use App\Services\Admin\BlogCategoryService;

// ===== BlogPost =====
use App\Interfaces\Repositories\Admin\BlogPostRepositoryInterface;
use App\Repositories\Admin\BlogPostRepository;
use App\Interfaces\Services\Admin\BlogPostServiceInterface;
use App\Services\Admin\BlogPostService;

// ===== BlogImage =====
use App\Interfaces\Repositories\Admin\BlogImageRepositoryInterface;
use App\Repositories\Admin\BlogImageRepository;
use App\Interfaces\Services\Admin\BlogImageServiceInterface;
use App\Services\Admin\BlogImageService;

// ===== Catalog =====
use App\Interfaces\Repositories\Admin\CatalogRepositoryInterface;
use App\Repositories\Admin\CatalogRepository;
use App\Interfaces\Services\Admin\CatalogServiceInterface;
use App\Services\Admin\CatalogService;

// ===== Certificate =====
use App\Interfaces\Repositories\Admin\CertificateRepositoryInterface;
use App\Repositories\Admin\CertificateRepository;
use App\Interfaces\Services\Admin\CertificateServiceInterface;
use App\Services\Admin\CertificateService;

// ===== Company =====
use App\Interfaces\Repositories\Admin\CompanyRepositoryInterface;
use App\Repositories\Admin\CompanyRepository;
use App\Interfaces\Services\Admin\CompanyServiceInterface;
use App\Services\Admin\CompanyService;

// ===== Currency =====
use App\Interfaces\Repositories\Admin\CurrencyRepositoryInterface;
use App\Repositories\Admin\CurrencyRepository;
use App\Interfaces\Services\Admin\CurrencyServiceInterface;
use App\Services\Admin\CurrencyService;

// ===== Dashboard =====
use App\Interfaces\Repositories\Admin\DashboardRepositoryInterface;
use App\Repositories\Admin\DashboardRepository;
use App\Interfaces\Services\Admin\DashboardServiceInterface;
use App\Services\Admin\DashboardService;

// ===== Faq =====
use App\Interfaces\Repositories\Admin\FaqRepositoryInterface;
use App\Repositories\Admin\FaqRepository;
use App\Interfaces\Services\Admin\FaqServiceInterface;
use App\Services\Admin\FaqService;

// ===== Product =====
use App\Interfaces\Repositories\Admin\ProductRepositoryInterface;
use App\Repositories\Admin\ProductRepository;
use App\Interfaces\Services\Admin\ProductServiceInterface;
use App\Services\Admin\ProductService;

// ===== ProductCategory =====
use App\Interfaces\Repositories\Admin\ProductCategoryRepositoryInterface;
use App\Repositories\Admin\ProductCategoryRepository;
use App\Interfaces\Services\Admin\ProductCategoryServiceInterface;
use App\Services\Admin\ProductCategoryService;

// ===== ProductImage =====
use App\Interfaces\Repositories\Admin\ProductImageRepositoryInterface;
use App\Repositories\Admin\ProductImageRepository;
use App\Interfaces\Services\Admin\ProductImageServiceInterface;
use App\Services\Admin\ProductImageService;

// ===== SeoPage =====
use App\Interfaces\Repositories\Admin\SeoPageRepositoryInterface;
use App\Repositories\Admin\SeoPageRepository;
use App\Interfaces\Services\Admin\SeoPageServiceInterface;
use App\Services\Admin\SeoPageService;

// ===== Testimonial =====
use App\Interfaces\Repositories\Admin\TestimonialRepositoryInterface;
use App\Repositories\Admin\TestimonialRepository;
use App\Interfaces\Services\Admin\TestimonialServiceInterface;
use App\Services\Admin\TestimonialService;

// ===== Vacancy =====
use App\Interfaces\Repositories\Admin\VacancyRepositoryInterface;
use App\Repositories\Admin\VacancyRepository;
use App\Interfaces\Services\Admin\VacancyServiceInterface;
use App\Services\Admin\VacancyService;

// ===== SocialNetwork =====
use App\Interfaces\Repositories\Admin\SocialNetworkRepositoryInterface;
use App\Repositories\Admin\SocialNetworkRepository;
use App\Interfaces\Services\Admin\SocialNetworkServiceInterface;
use App\Services\Admin\SocialNetworkService;

// ===== WhyChooseUs =====
use App\Interfaces\Repositories\Admin\WhyChooseUsRepositoryInterface;
use App\Repositories\Admin\WhyChooseUsRepository;
use App\Interfaces\Services\Admin\WhyChooseUsServiceInterface;
use App\Services\Admin\WhyChooseUsService;

// ===== SimpleImage =====
use App\Interfaces\Repositories\Admin\SimpleImageRepositoryInterface;
use App\Repositories\Admin\SimpleImageRepository;
use App\Interfaces\Services\Admin\SimpleImageServiceInterface;
use App\Services\Admin\SimpleImageService;

// ===== ContactMessage =====
use App\Interfaces\Repositories\Admin\ContactMessageRepositoryInterface;
use App\Repositories\Admin\ContactMessageRepository;
use App\Interfaces\Services\Admin\ContactMessageServiceInterface;
use App\Services\Admin\ContactMessageService;

// ===== Comment =====
use App\Interfaces\Repositories\Admin\CommentRepositoryInterface;
use App\Repositories\Admin\CommentRepository;
use App\Interfaces\Services\Admin\CommentServiceInterface;
use App\Services\Admin\CommentService;

// ===== Dictionary =====
use App\Interfaces\Repositories\Admin\DictionaryRepositoryInterface;
use App\Repositories\Admin\DictionaryRepository;
use App\Interfaces\Services\Admin\DictionaryServiceInterface;
use App\Services\Admin\DictionaryService;

// ===== Page =====
use App\Interfaces\Repositories\Admin\PageRepositoryInterface;
use App\Repositories\Admin\PageRepository;
use App\Interfaces\Services\Admin\PageServiceInterface;
use App\Services\Admin\PageService;

// ===== Project =====
use App\Interfaces\Repositories\Admin\ProjectRepositoryInterface;
use App\Repositories\Admin\ProjectRepository;
use App\Interfaces\Services\Admin\ProjectServiceInterface;
use App\Services\Admin\ProjectService;

// ===== Service =====
use App\Interfaces\Repositories\Admin\ServiceRepositoryInterface;
use App\Repositories\Admin\ServiceRepository;
use App\Interfaces\Services\Admin\ServiceServiceInterface;
use App\Services\Admin\ServiceService;

use App\Interfaces\Repositories\Admin\AllSeoPagesRepositoryInterface;
use App\Repositories\Admin\AllSeoPagesRepository;
use App\Interfaces\Services\Admin\AllSeoPagesServiceInterface;
use App\Services\Admin\AllSeoPagesService;
use App\Interfaces\Repositories\Admin\BecomeAInvestorAdvantageRepositoryInterface;
use App\Repositories\Admin\BecomeAInvestorAdvantageRepository;
use App\Interfaces\Services\Admin\BecomeAInvestorAdvantageServiceInterface;
use App\Services\Admin\BecomeAInvestorAdvantageService;
use App\Interfaces\Repositories\Admin\BecomeAInvestorRepositoryInterface;
use App\Repositories\Admin\BecomeAInvestorRepository;
use App\Interfaces\Services\Admin\BecomeAInvestorServiceInterface;
use App\Services\Admin\BecomeAInvestorService;
use App\Interfaces\Repositories\Admin\BecomeAInvestorFormRepositoryInterface;
use App\Repositories\Admin\BecomeAInvestorFormRepository;
use App\Interfaces\Services\Admin\BecomeAInvestorFormServiceInterface;
use App\Services\Admin\BecomeAInvestorFormService;
use App\Interfaces\Repositories\Admin\BecomeAPartnerAdvantageRepositoryInterface;
use App\Repositories\Admin\BecomeAPartnerAdvantageRepository;
use App\Interfaces\Services\Admin\BecomeAPartnerAdvantageServiceInterface;
use App\Services\Admin\BecomeAPartnerAdvantageService;
use App\Interfaces\Repositories\Admin\BecomeAPartnerRepositoryInterface;
use App\Repositories\Admin\BecomeAPartnerRepository;
use App\Interfaces\Services\Admin\BecomeAPartnerServiceInterface;
use App\Services\Admin\BecomeAPartnerService;
use App\Interfaces\Repositories\Admin\BecomeAPartnerFormRepositoryInterface;
use App\Repositories\Admin\BecomeAPartnerFormRepository;
use App\Interfaces\Services\Admin\BecomeAPartnerFormServiceInterface;
use App\Services\Admin\BecomeAPartnerFormService;
use App\Interfaces\Repositories\Admin\CompanyManagementRepositoryInterface;
use App\Repositories\Admin\CompanyManagementRepository;
use App\Interfaces\Services\Admin\CompanyManagementServiceInterface;
use App\Services\Admin\CompanyManagementService;
use App\Interfaces\Repositories\Admin\CreditCardRepositoryInterface;
use App\Repositories\Admin\CreditCardRepository;
use App\Interfaces\Services\Admin\CreditCardServiceInterface;
use App\Services\Admin\CreditCardService;
use App\Interfaces\Repositories\Admin\CustomerRightsRepositoryInterface;
use App\Repositories\Admin\CustomerRightsRepository;
use App\Interfaces\Services\Admin\CustomerRightsServiceInterface;
use App\Services\Admin\CustomerRightsService;
use App\Interfaces\Repositories\Admin\DeliveryPaymentRepositoryInterface;
use App\Repositories\Admin\DeliveryPaymentRepository;
use App\Interfaces\Services\Admin\DeliveryPaymentServiceInterface;
use App\Services\Admin\DeliveryPaymentService;
use App\Interfaces\Repositories\Admin\EmailRepositoryInterface;
use App\Repositories\Admin\EmailRepository;
use App\Interfaces\Services\Admin\EmailServiceInterface;
use App\Services\Admin\EmailService;
use App\Interfaces\Repositories\Admin\HeroCategoryRepositoryInterface;
use App\Repositories\Admin\HeroCategoryRepository;
use App\Interfaces\Services\Admin\HeroCategoryServiceInterface;
use App\Services\Admin\HeroCategoryService;
use App\Interfaces\Repositories\Admin\HeroProductRepositoryInterface;
use App\Repositories\Admin\HeroProductRepository;
use App\Interfaces\Services\Admin\HeroProductServiceInterface;
use App\Services\Admin\HeroProductService;
use App\Interfaces\Repositories\Admin\HomePageCategoryRepositoryInterface;
use App\Repositories\Admin\HomePageCategoryRepository;
use App\Interfaces\Services\Admin\HomePageCategoryServiceInterface;
use App\Services\Admin\HomePageCategoryService;
use App\Interfaces\Repositories\Admin\HomePageDiscountRepositoryInterface;
use App\Repositories\Admin\HomePageDiscountRepository;
use App\Interfaces\Services\Admin\HomePageDiscountServiceInterface;
use App\Services\Admin\HomePageDiscountService;
use App\Interfaces\Repositories\Admin\HomePageProjectCategoryRepositoryInterface;
use App\Repositories\Admin\HomePageProjectCategoryRepository;
use App\Interfaces\Services\Admin\HomePageProjectCategoryServiceInterface;
use App\Services\Admin\HomePageProjectCategoryService;
use App\Interfaces\Repositories\Admin\HomePageProjectRepositoryInterface;
use App\Repositories\Admin\HomePageProjectRepository;
use App\Interfaces\Services\Admin\HomePageProjectServiceInterface;
use App\Services\Admin\HomePageProjectService;
use App\Interfaces\Repositories\Admin\IndividualProjectOrderRepositoryInterface;
use App\Repositories\Admin\IndividualProjectOrderRepository;
use App\Interfaces\Services\Admin\IndividualProjectOrderServiceInterface;
use App\Services\Admin\IndividualProjectOrderService;
use App\Interfaces\Repositories\Admin\KivCategoryRepositoryInterface;
use App\Repositories\Admin\KivCategoryRepository;
use App\Interfaces\Services\Admin\KivCategoryServiceInterface;
use App\Services\Admin\KivCategoryService;
use App\Interfaces\Repositories\Admin\KivRepositoryInterface;
use App\Repositories\Admin\KivRepository;
use App\Interfaces\Services\Admin\KivServiceInterface;
use App\Services\Admin\KivService;
use App\Interfaces\Repositories\Admin\MapRepositoryInterface;
use App\Repositories\Admin\MapRepository;
use App\Interfaces\Services\Admin\MapServiceInterface;
use App\Services\Admin\MapService;
use App\Interfaces\Repositories\Admin\NewsCategoryRepositoryInterface;
use App\Repositories\Admin\NewsCategoryRepository;
use App\Interfaces\Services\Admin\NewsCategoryServiceInterface;
use App\Services\Admin\NewsCategoryService;
use App\Interfaces\Repositories\Admin\NewsPostRepositoryInterface;
use App\Repositories\Admin\NewsPostRepository;
use App\Interfaces\Services\Admin\NewsPostServiceInterface;
use App\Services\Admin\NewsPostService;
use App\Interfaces\Repositories\Admin\NewsImageRepositoryInterface;
use App\Repositories\Admin\NewsImageRepository;
use App\Interfaces\Services\Admin\NewsImageServiceInterface;
use App\Services\Admin\NewsImageService;
use App\Interfaces\Repositories\Admin\OurGoalRepositoryInterface;
use App\Repositories\Admin\OurGoalRepository;
use App\Interfaces\Services\Admin\OurGoalServiceInterface;
use App\Services\Admin\OurGoalService;
use App\Interfaces\Repositories\Admin\PartnerSliderRepositoryInterface;
use App\Repositories\Admin\PartnerSliderRepository;
use App\Interfaces\Services\Admin\PartnerSliderServiceInterface;
use App\Services\Admin\PartnerSliderService;
use App\Interfaces\Repositories\Admin\OrderRepositoryInterface;
use App\Repositories\Admin\OrderRepository;
use App\Interfaces\Services\Admin\OrderServiceInterface;
use App\Services\Admin\OrderService;
use App\Interfaces\Repositories\Admin\PhoneRepositoryInterface;
use App\Repositories\Admin\PhoneRepository;
use App\Interfaces\Services\Admin\PhoneServiceInterface;
use App\Services\Admin\PhoneService;
use App\Interfaces\Repositories\Admin\PhoneTypeRepositoryInterface;
use App\Repositories\Admin\PhoneTypeRepository;
use App\Interfaces\Services\Admin\PhoneTypeServiceInterface;
use App\Services\Admin\PhoneTypeService;
use App\Interfaces\Repositories\Admin\PopupAdRepositoryInterface;
use App\Repositories\Admin\PopupAdRepository;
use App\Interfaces\Services\Admin\PopupAdServiceInterface;
use App\Services\Admin\PopupAdService;
use App\Interfaces\Repositories\Admin\PrivacyPolicyRepositoryInterface;
use App\Repositories\Admin\PrivacyPolicyRepository;
use App\Interfaces\Services\Admin\PrivacyPolicyServiceInterface;
use App\Services\Admin\PrivacyPolicyService;
use App\Interfaces\Repositories\Admin\ProductAccordionRepositoryInterface;
use App\Repositories\Admin\ProductAccordionRepository;
use App\Interfaces\Services\Admin\ProductAccordionServiceInterface;
use App\Services\Admin\ProductAccordionService;
use App\Interfaces\Repositories\Admin\ProductColorRepositoryInterface;
use App\Repositories\Admin\ProductColorRepository;
use App\Interfaces\Services\Admin\ProductColorServiceInterface;
use App\Services\Admin\ProductColorService;
use App\Interfaces\Repositories\Admin\ProductDetailRepositoryInterface;
use App\Repositories\Admin\ProductDetailRepository;
use App\Interfaces\Services\Admin\ProductDetailServiceInterface;
use App\Services\Admin\ProductDetailService;
use App\Interfaces\Repositories\Admin\ProductDetailValueRepositoryInterface;
use App\Repositories\Admin\ProductDetailValueRepository;
use App\Interfaces\Services\Admin\ProductDetailValueServiceInterface;
use App\Services\Admin\ProductDetailValueService;
use App\Interfaces\Repositories\Admin\ProductDetailsValuesDetailRepositoryInterface;
use App\Repositories\Admin\ProductDetailsValuesDetailRepository;
use App\Interfaces\Services\Admin\ProductDetailsValuesDetailServiceInterface;
use App\Services\Admin\ProductDetailsValuesDetailService;
use App\Interfaces\Repositories\Admin\ProductPageCategoryRepositoryInterface;
use App\Repositories\Admin\ProductPageCategoryRepository;
use App\Interfaces\Services\Admin\ProductPageCategoryServiceInterface;
use App\Services\Admin\ProductPageCategoryService;
use App\Interfaces\Repositories\Admin\ProductPriceFilterRepositoryInterface;
use App\Repositories\Admin\ProductPriceFilterRepository;
use App\Interfaces\Services\Admin\ProductPriceFilterServiceInterface;
use App\Services\Admin\ProductPriceFilterService;
use App\Interfaces\Repositories\Admin\ProductTagRepositoryInterface;
use App\Repositories\Admin\ProductTagRepository;
use App\Interfaces\Services\Admin\ProductTagServiceInterface;
use App\Services\Admin\ProductTagService;
use App\Interfaces\Repositories\Admin\ProductTypeRepositoryInterface;
use App\Repositories\Admin\ProductTypeRepository;
use App\Interfaces\Services\Admin\ProductTypeServiceInterface;
use App\Services\Admin\ProductTypeService;
use App\Interfaces\Repositories\Admin\ProductTypeProductRepositoryInterface;
use App\Repositories\Admin\ProductTypeProductRepository;
use App\Interfaces\Services\Admin\ProductTypeProductServiceInterface;
use App\Services\Admin\ProductTypeProductService;
use App\Interfaces\Repositories\Admin\ProjectCategoryRepositoryInterface;
use App\Repositories\Admin\ProjectCategoryRepository;
use App\Interfaces\Services\Admin\ProjectCategoryServiceInterface;
use App\Services\Admin\ProjectCategoryService;
use App\Interfaces\Repositories\Admin\ProjectImageRepositoryInterface;
use App\Repositories\Admin\ProjectImageRepository;
use App\Interfaces\Services\Admin\ProjectImageServiceInterface;
use App\Services\Admin\ProjectImageService;
use App\Interfaces\Repositories\Admin\ProjectVideoRepositoryInterface;
use App\Repositories\Admin\ProjectVideoRepository;
use App\Interfaces\Services\Admin\ProjectVideoServiceInterface;
use App\Services\Admin\ProjectVideoService;
use App\Interfaces\Repositories\Admin\RefundPolicyRepositoryInterface;
use App\Repositories\Admin\RefundPolicyRepository;
use App\Interfaces\Services\Admin\RefundPolicyServiceInterface;
use App\Services\Admin\RefundPolicyService;
use App\Interfaces\Repositories\Admin\ReportRepositoryInterface;
use App\Repositories\Admin\ReportRepository;
use App\Interfaces\Services\Admin\ReportServiceInterface;
use App\Services\Admin\ReportService;
use App\Interfaces\Repositories\Admin\ServiceAdvantageRepositoryInterface;
use App\Repositories\Admin\ServiceAdvantageRepository;
use App\Interfaces\Services\Admin\ServiceAdvantageServiceInterface;
use App\Services\Admin\ServiceAdvantageService;
use App\Interfaces\Repositories\Admin\ServiceStaticDataRepositoryInterface;
use App\Repositories\Admin\ServiceStaticDataRepository;
use App\Interfaces\Services\Admin\ServiceStaticDataServiceInterface;
use App\Services\Admin\ServiceStaticDataService;
use App\Interfaces\Repositories\Admin\SubscriberRepositoryInterface;
use App\Repositories\Admin\SubscriberRepository;
use App\Interfaces\Services\Admin\SubscriberServiceInterface;
use App\Services\Admin\SubscriberService;
use App\Interfaces\Repositories\Admin\SuggestAPlaceRepositoryInterface;
use App\Repositories\Admin\SuggestAPlaceRepository;
use App\Interfaces\Services\Admin\SuggestAPlaceServiceInterface;
use App\Services\Admin\SuggestAPlaceService;
use App\Interfaces\Repositories\Admin\SuggestAPlaceFormRepositoryInterface;
use App\Repositories\Admin\SuggestAPlaceFormRepository;
use App\Interfaces\Services\Admin\SuggestAPlaceFormServiceInterface;
use App\Services\Admin\SuggestAPlaceFormService;
use App\Interfaces\Repositories\Admin\SuggestionRepositoryInterface;
use App\Repositories\Admin\SuggestionRepository;
use App\Interfaces\Services\Admin\SuggestionServiceInterface;
use App\Services\Admin\SuggestionService;
use App\Interfaces\Repositories\Admin\SuggestionsComplaintsRepositoryInterface;
use App\Repositories\Admin\SuggestionsComplaintsRepository;
use App\Interfaces\Services\Admin\SuggestionsComplaintsServiceInterface;
use App\Services\Admin\SuggestionsComplaintsService;
use App\Interfaces\Repositories\Admin\SuggestionsComplaintsFormRepositoryInterface;
use App\Repositories\Admin\SuggestionsComplaintsFormRepository;
use App\Interfaces\Services\Admin\SuggestionsComplaintsFormServiceInterface;
use App\Services\Admin\SuggestionsComplaintsFormService;
use App\Interfaces\Repositories\Admin\UserRepositoryInterface;
use App\Repositories\Admin\UserRepository;
use App\Interfaces\Services\Admin\UserServiceInterface;
use App\Services\Admin\UserService;
use App\Interfaces\Repositories\Admin\VacancyFormRepositoryInterface;
use App\Repositories\Admin\VacancyFormRepository;
use App\Interfaces\Services\Admin\VacancyFormServiceInterface;
use App\Services\Admin\VacancyFormService;
use App\Interfaces\Repositories\Admin\VacancyRequirementRepositoryInterface;
use App\Repositories\Admin\VacancyRequirementRepository;
use App\Interfaces\Services\Admin\VacancyRequirementServiceInterface;
use App\Services\Admin\VacancyRequirementService;
use App\Interfaces\Repositories\Admin\WhyChooseUsSliderRepositoryInterface;
use App\Repositories\Admin\WhyChooseUsSliderRepository;
use App\Interfaces\Services\Admin\WhyChooseUsSliderServiceInterface;
use App\Services\Admin\WhyChooseUsSliderService;
use App\Interfaces\Repositories\Admin\ServiceFormRepositoryInterface;
use App\Repositories\Admin\ServiceFormRepository;
use App\Interfaces\Services\Admin\ServiceFormServiceInterface;
use App\Services\Admin\ServiceFormService;

use App\Interfaces\Repositories\Admin\SiteUsageRulesRepositoryInterface;
use App\Repositories\Admin\SiteUsageRulesRepository;
use App\Interfaces\Services\Admin\SiteUsageRulesServiceInterface;
use App\Services\Admin\SiteUsageRulesService;
use App\Interfaces\Repositories\Admin\WarrantyPolicyRepositoryInterface;
use App\Repositories\Admin\WarrantyPolicyRepository;
use App\Interfaces\Services\Admin\WarrantyPolicyServiceInterface;
use App\Services\Admin\WarrantyPolicyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
        $this->registerServices();

        // Dynamically register Front Service/Repository bindings
        $frontModules = [
            'About', 'Action', 'Address', 'Administration', 'BecomeAInvestor', 'BecomeAInvestorAdvantage',
            'BecomeAPartner', 'BecomeAPartnerAdvantage', 'BlogCategory', 'BlogPost', 'Catalog', 'Certificate',
            'Company', 'CompanyManagement', 'CreditCard', 'Currency', 'Dictionary',
            'Faq', 'HeroCategory', 'HeroProduct', 'HomePageCategory', 'HomePageDiscount', 'HomePageProjectCategory', 'IndividualProjectOrder', 'Kiv', 'KivCategory',
            'Language', 'Map', 'NewsCategory', 'NewsPost', 'OurGoal',
            'Page', 'PartnerSlider', 'Phone', 'PhoneType', 'PopupAd', 'PrivacyPolicy',
            'Product', 'ProductType', 'ProjectCategory', 'Project', 'RefundPolicy', 'Report',
            'RoluteeteAction', 'SeoPage', 'Service', 'ServiceStaticData', 'SimpleImage', 'SocialNetwork',
            'Subscriber', 'Suggestion', 'SuggestAPlace', 'SuggestAPlaceForm', 'SuggestionsComplaints', 'SuggestionsComplaintsForm', 'Testimonial', 'Vacancy',
            'VacancyForm', 'WarrantyPolicy', 'WhyChooseUs', 'WhyChooseUsSlider'
        ];

        foreach ($frontModules as $module) {
            $this->app->bind(
                "App\\Interfaces\\Repositories\\Front\\{$module}RepositoryInterface",
                "App\\Repositories\\Front\\{$module}Repository"
            );
            $this->app->bind(
                "App\\Interfaces\\Services\\Front\\{$module}ServiceInterface",
                "App\\Services\\Front\\{$module}Service"
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Repository interface → concrete class bind-ları
     */
    private function registerRepositories(): void
    {
        $bindings = [
            SiteUsageRulesRepositoryInterface::class => SiteUsageRulesRepository::class,
            WarrantyPolicyRepositoryInterface::class => WarrantyPolicyRepository::class,
            AllSeoPagesRepositoryInterface::class => AllSeoPagesRepository::class,
            BecomeAInvestorAdvantageRepositoryInterface::class => BecomeAInvestorAdvantageRepository::class,
            BecomeAInvestorRepositoryInterface::class => BecomeAInvestorRepository::class,
            BecomeAInvestorFormRepositoryInterface::class => BecomeAInvestorFormRepository::class,
            BecomeAPartnerAdvantageRepositoryInterface::class => BecomeAPartnerAdvantageRepository::class,
            BecomeAPartnerRepositoryInterface::class => BecomeAPartnerRepository::class,
            BecomeAPartnerFormRepositoryInterface::class => BecomeAPartnerFormRepository::class,
            CompanyManagementRepositoryInterface::class => CompanyManagementRepository::class,
            CreditCardRepositoryInterface::class => CreditCardRepository::class,
            CustomerRightsRepositoryInterface::class => CustomerRightsRepository::class,
            DeliveryPaymentRepositoryInterface::class => DeliveryPaymentRepository::class,
            EmailRepositoryInterface::class => EmailRepository::class,
            HeroCategoryRepositoryInterface::class => HeroCategoryRepository::class,
            HeroProductRepositoryInterface::class => HeroProductRepository::class,
            HomePageCategoryRepositoryInterface::class => HomePageCategoryRepository::class,
            HomePageDiscountRepositoryInterface::class => HomePageDiscountRepository::class,
            HomePageProjectCategoryRepositoryInterface::class => HomePageProjectCategoryRepository::class,
            HomePageProjectRepositoryInterface::class => HomePageProjectRepository::class,
            IndividualProjectOrderRepositoryInterface::class => IndividualProjectOrderRepository::class,
            KivCategoryRepositoryInterface::class => KivCategoryRepository::class,
            KivRepositoryInterface::class => KivRepository::class,
            MapRepositoryInterface::class => MapRepository::class,
            NewsCategoryRepositoryInterface::class => NewsCategoryRepository::class,
            NewsPostRepositoryInterface::class => NewsPostRepository::class,
            NewsImageRepositoryInterface::class => NewsImageRepository::class,
            OurGoalRepositoryInterface::class => OurGoalRepository::class,
            PartnerSliderRepositoryInterface::class => PartnerSliderRepository::class,
            OrderRepositoryInterface::class => OrderRepository::class,
            PhoneRepositoryInterface::class => PhoneRepository::class,
            PhoneTypeRepositoryInterface::class => PhoneTypeRepository::class,
            PopupAdRepositoryInterface::class => PopupAdRepository::class,
            PrivacyPolicyRepositoryInterface::class => PrivacyPolicyRepository::class,
            ProductAccordionRepositoryInterface::class => ProductAccordionRepository::class,
            ProductColorRepositoryInterface::class => ProductColorRepository::class,
            ProductDetailRepositoryInterface::class => ProductDetailRepository::class,
            ProductDetailValueRepositoryInterface::class => ProductDetailValueRepository::class,
            ProductDetailsValuesDetailRepositoryInterface::class => ProductDetailsValuesDetailRepository::class,
            ProductPageCategoryRepositoryInterface::class => ProductPageCategoryRepository::class,
            ProductPriceFilterRepositoryInterface::class => ProductPriceFilterRepository::class,
            ProductTagRepositoryInterface::class => ProductTagRepository::class,
            ProductTypeRepositoryInterface::class => ProductTypeRepository::class,
            ProductTypeProductRepositoryInterface::class => ProductTypeProductRepository::class,
            ProjectCategoryRepositoryInterface::class => ProjectCategoryRepository::class,
            ProjectImageRepositoryInterface::class => ProjectImageRepository::class,
            ProjectVideoRepositoryInterface::class => ProjectVideoRepository::class,
            RefundPolicyRepositoryInterface::class => RefundPolicyRepository::class,
            ReportRepositoryInterface::class => ReportRepository::class,
            ServiceAdvantageRepositoryInterface::class => ServiceAdvantageRepository::class,
            ServiceStaticDataRepositoryInterface::class => ServiceStaticDataRepository::class,
            SubscriberRepositoryInterface::class => SubscriberRepository::class,
            SuggestAPlaceRepositoryInterface::class => SuggestAPlaceRepository::class,
            SuggestAPlaceFormRepositoryInterface::class => SuggestAPlaceFormRepository::class,
            SuggestionRepositoryInterface::class => SuggestionRepository::class,
            SuggestionsComplaintsRepositoryInterface::class => SuggestionsComplaintsRepository::class,
            SuggestionsComplaintsFormRepositoryInterface::class => SuggestionsComplaintsFormRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
            VacancyFormRepositoryInterface::class => VacancyFormRepository::class,
            VacancyRequirementRepositoryInterface::class => VacancyRequirementRepository::class,
            WhyChooseUsSliderRepositoryInterface::class => WhyChooseUsSliderRepository::class,
            ServiceFormRepositoryInterface::class => ServiceFormRepository::class,
            LanguageRepositoryInterface::class => LanguageRepository::class,
            AboutRepositoryInterface::class => AboutRepository::class,
            ActionRepositoryInterface::class => ActionRepository::class,
            AddressRepositoryInterface::class => AddressRepository::class,
            AddressTypeRepositoryInterface::class => AddressTypeRepository::class,
            AdministrationRepositoryInterface::class => AdministrationRepository::class,
            BlogCategoryRepositoryInterface::class => BlogCategoryRepository::class,
            BlogPostRepositoryInterface::class => BlogPostRepository::class,
            BlogImageRepositoryInterface::class => BlogImageRepository::class,
            CatalogRepositoryInterface::class => CatalogRepository::class,
            CertificateRepositoryInterface::class => CertificateRepository::class,
            CompanyRepositoryInterface::class => CompanyRepository::class,
            CurrencyRepositoryInterface::class => CurrencyRepository::class,
            DashboardRepositoryInterface::class => DashboardRepository::class,
            FaqRepositoryInterface::class => FaqRepository::class,
            ProductRepositoryInterface::class => ProductRepository::class,
            ProductCategoryRepositoryInterface::class => ProductCategoryRepository::class,
            ProductImageRepositoryInterface::class => ProductImageRepository::class,
            SeoPageRepositoryInterface::class => SeoPageRepository::class,
            TestimonialRepositoryInterface::class => TestimonialRepository::class,
            VacancyRepositoryInterface::class => VacancyRepository::class,
            SocialNetworkRepositoryInterface::class => SocialNetworkRepository::class,
            WhyChooseUsRepositoryInterface::class => WhyChooseUsRepository::class,
            SimpleImageRepositoryInterface::class => SimpleImageRepository::class,
            ContactMessageRepositoryInterface::class => ContactMessageRepository::class,
            CommentRepositoryInterface::class => CommentRepository::class,
            DictionaryRepositoryInterface::class => DictionaryRepository::class,
            PageRepositoryInterface::class => PageRepository::class,
            ProjectRepositoryInterface::class => ProjectRepository::class,
            ServiceRepositoryInterface::class => ServiceRepository::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Service interface → concrete class bind-ları
     */
    private function registerServices(): void
    {
        $bindings = [
            SiteUsageRulesServiceInterface::class => SiteUsageRulesService::class,
            WarrantyPolicyServiceInterface::class => WarrantyPolicyService::class,
            AllSeoPagesServiceInterface::class => AllSeoPagesService::class,
            BecomeAInvestorAdvantageServiceInterface::class => BecomeAInvestorAdvantageService::class,
            BecomeAInvestorServiceInterface::class => BecomeAInvestorService::class,
            BecomeAInvestorFormServiceInterface::class => BecomeAInvestorFormService::class,
            BecomeAPartnerAdvantageServiceInterface::class => BecomeAPartnerAdvantageService::class,
            BecomeAPartnerServiceInterface::class => BecomeAPartnerService::class,
            BecomeAPartnerFormServiceInterface::class => BecomeAPartnerFormService::class,
            CompanyManagementServiceInterface::class => CompanyManagementService::class,
            CreditCardServiceInterface::class => CreditCardService::class,
            CustomerRightsServiceInterface::class => CustomerRightsService::class,
            DeliveryPaymentServiceInterface::class => DeliveryPaymentService::class,
            EmailServiceInterface::class => EmailService::class,
            HeroCategoryServiceInterface::class => HeroCategoryService::class,
            HeroProductServiceInterface::class => HeroProductService::class,
            HomePageCategoryServiceInterface::class => HomePageCategoryService::class,
            HomePageDiscountServiceInterface::class => HomePageDiscountService::class,
            HomePageProjectCategoryServiceInterface::class => HomePageProjectCategoryService::class,
            HomePageProjectServiceInterface::class => HomePageProjectService::class,
            IndividualProjectOrderServiceInterface::class => IndividualProjectOrderService::class,
            KivCategoryServiceInterface::class => KivCategoryService::class,
            KivServiceInterface::class => KivService::class,
            MapServiceInterface::class => MapService::class,
            NewsCategoryServiceInterface::class => NewsCategoryService::class,
            NewsPostServiceInterface::class => NewsPostService::class,
            NewsImageServiceInterface::class => NewsImageService::class,
            OurGoalServiceInterface::class => OurGoalService::class,
            PartnerSliderServiceInterface::class => PartnerSliderService::class,
            OrderServiceInterface::class => OrderService::class,
            PhoneServiceInterface::class => PhoneService::class,
            PhoneTypeServiceInterface::class => PhoneTypeService::class,
            PopupAdServiceInterface::class => PopupAdService::class,
            PrivacyPolicyServiceInterface::class => PrivacyPolicyService::class,
            ProductAccordionServiceInterface::class => ProductAccordionService::class,
            ProductColorServiceInterface::class => ProductColorService::class,
            ProductDetailServiceInterface::class => ProductDetailService::class,
            ProductDetailValueServiceInterface::class => ProductDetailValueService::class,
            ProductDetailsValuesDetailServiceInterface::class => ProductDetailsValuesDetailService::class,
            ProductPageCategoryServiceInterface::class => ProductPageCategoryService::class,
            ProductPriceFilterServiceInterface::class => ProductPriceFilterService::class,
            ProductTagServiceInterface::class => ProductTagService::class,
            ProductTypeServiceInterface::class => ProductTypeService::class,
            ProductTypeProductServiceInterface::class => ProductTypeProductService::class,
            ProjectCategoryServiceInterface::class => ProjectCategoryService::class,
            ProjectImageServiceInterface::class => ProjectImageService::class,
            ProjectVideoServiceInterface::class => ProjectVideoService::class,
            RefundPolicyServiceInterface::class => RefundPolicyService::class,
            ReportServiceInterface::class => ReportService::class,
            ServiceAdvantageServiceInterface::class => ServiceAdvantageService::class,
            ServiceStaticDataServiceInterface::class => ServiceStaticDataService::class,
            SubscriberServiceInterface::class => SubscriberService::class,
            SuggestAPlaceServiceInterface::class => SuggestAPlaceService::class,
            SuggestAPlaceFormServiceInterface::class => SuggestAPlaceFormService::class,
            SuggestionServiceInterface::class => SuggestionService::class,
            SuggestionsComplaintsServiceInterface::class => SuggestionsComplaintsService::class,
            SuggestionsComplaintsFormServiceInterface::class => SuggestionsComplaintsFormService::class,
            UserServiceInterface::class => UserService::class,
            VacancyFormServiceInterface::class => VacancyFormService::class,
            VacancyRequirementServiceInterface::class => VacancyRequirementService::class,
            WhyChooseUsSliderServiceInterface::class => WhyChooseUsSliderService::class,
            ServiceFormServiceInterface::class => ServiceFormService::class,
            LanguageServiceInterface::class => LanguageService::class,
            AboutServiceInterface::class => AboutService::class,
            ActionServiceInterface::class => ActionService::class,
            AddressServiceInterface::class => AddressService::class,
            AddressTypeServiceInterface::class => AddressTypeService::class,
            AdministrationServiceInterface::class => AdministrationService::class,
            BlogCategoryServiceInterface::class => BlogCategoryService::class,
            BlogPostServiceInterface::class => BlogPostService::class,
            BlogImageServiceInterface::class => BlogImageService::class,
            CatalogServiceInterface::class => CatalogService::class,
            CertificateServiceInterface::class => CertificateService::class,
            CompanyServiceInterface::class => CompanyService::class,
            CurrencyServiceInterface::class => CurrencyService::class,
            DashboardServiceInterface::class => DashboardService::class,
            FaqServiceInterface::class => FaqService::class,
            ProductServiceInterface::class => ProductService::class,
            ProductCategoryServiceInterface::class => ProductCategoryService::class,
            ProductImageServiceInterface::class => ProductImageService::class,
            SeoPageServiceInterface::class => SeoPageService::class,
            TestimonialServiceInterface::class => TestimonialService::class,
            VacancyServiceInterface::class => VacancyService::class,
            SocialNetworkServiceInterface::class => SocialNetworkService::class,
            WhyChooseUsServiceInterface::class => WhyChooseUsService::class,
            SimpleImageServiceInterface::class => SimpleImageService::class,
            ContactMessageServiceInterface::class => ContactMessageService::class,
            CommentServiceInterface::class => CommentService::class,
            DictionaryServiceInterface::class => DictionaryService::class,
            PageServiceInterface::class => PageService::class,
            ProjectServiceInterface::class => ProjectService::class,
            ServiceServiceInterface::class => ServiceService::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
}
