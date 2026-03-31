<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    protected $signature = 'translations:seed {--force : Overwrite existing translations}';
    protected $description = 'Seed English and Arabic base translations into the database';

    public function handle()
    {
        $force = $this->option('force');

        $this->info('Seeding English translations...');
        $enCount = $this->seedLocale('en', $this->getEnglishTranslations(), $force);
        $this->info("  Seeded {$enCount} English translations.");

        $this->info('Seeding Arabic translations...');
        $arCount = $this->seedLocale('ar', $this->getArabicTranslations(), $force);
        $this->info("  Seeded {$arCount} Arabic translations.");

        $this->info('Done! Now run: php artisan translate:auto {locale} for other languages.');
        return 0;
    }

    private function seedLocale(string $locale, array $translations, bool $force): int
    {
        $count = 0;
        foreach ($translations as $fullKey => $value) {
            $parts = explode('.', $fullKey, 2);
            $group = $parts[0];
            $key = $parts[1] ?? $parts[0];

            if (!$force) {
                $exists = Translation::where('locale', $locale)->where('group', $group)->where('key', $key)->exists();
                if ($exists) continue;
            }

            Translation::setTranslation($locale, $group, $key, $value);
            $count++;
        }
        return $count;
    }

    private function getEnglishTranslations(): array
    {
        return [
            // Common
            'common.home' => 'Home', 'common.save' => 'Save', 'common.cancel' => 'Cancel',
            'common.delete' => 'Delete', 'common.edit' => 'Edit', 'common.create' => 'Create',
            'common.search' => 'Search', 'common.filter' => 'Filter', 'common.loading' => 'Loading...',
            'common.noData' => 'No data available', 'common.actions' => 'Actions', 'common.status' => 'Status',
            'common.date' => 'Date', 'common.amount' => 'Amount', 'common.total' => 'Total',
            'common.close' => 'Close', 'common.confirm' => 'Confirm', 'common.yes' => 'Yes', 'common.no' => 'No',
            'common.all' => 'All', 'common.active' => 'Active', 'common.inactive' => 'Inactive',
            'common.success' => 'Success', 'common.error' => 'Error', 'common.warning' => 'Warning',

            // Public Nav
            'publicNav.home' => 'Home', 'publicNav.services' => 'Services', 'publicNav.smmPanel' => 'SMM Panel',
            'publicNav.aboutUs' => 'About Us', 'publicNav.contactUs' => 'Contact Us', 'publicNav.faq' => 'FAQ',
            'publicNav.privacy' => 'Privacy', 'publicNav.howItWorks' => 'How It Works',
            'publicNav.signIn' => 'Sign In', 'publicNav.signUp' => 'Sign Up', 'publicNav.language' => 'Language',
            'publicNav.lightMode' => 'Light Mode', 'publicNav.darkMode' => 'Dark Mode',

            // Footer
            'footer.quickLinks' => 'Quick Links', 'footer.legal' => 'Legal', 'footer.contactUs' => 'Contact Us',
            'footer.supportAvailable' => '24/7 Support Available', 'footer.allRightsReserved' => 'All rights reserved',
            'footer.madeWith' => 'Made with', 'footer.forGrowth' => 'for growth',
            'footer.description' => 'The cheapest and most reliable SMM panel for Instagram, TikTok, YouTube, Facebook and more. Instant delivery with 24/7 support.',
            'footer.bestServices' => 'Best SMM Services',

            // Home
            'home.heroTitle' => 'Grow Your', 'home.heroTitleHighlight' => 'Social Media Instantly',
            'home.heroDescription' => 'The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Starting at just $0.01',
            'home.getStarted' => 'Get Started Free', 'home.viewServices' => 'View All Services',
            'home.trustedBy' => 'Trusted by', 'home.customers' => 'customers worldwide',
            'home.whyChooseUs' => 'Why Choose Us', 'home.whyChooseUsDesc' => 'We provide the best SMM services with unmatched quality and support',
            'home.instantDelivery' => 'Instant Delivery', 'home.instantDeliveryDesc' => 'Your orders start processing immediately after payment confirmation',
            'home.securePayment' => 'Secure Payment', 'home.securePaymentDesc' => 'Multiple payment methods with bank-level security encryption',
            'home.support247' => '24/7 Support', 'home.support247Desc' => 'Our dedicated team is always ready to help you anytime',
            'home.bestPrices' => 'Cheapest Prices', 'home.bestPricesDesc' => 'Competitive pricing with the best quality services in the market',
            'home.popularServices' => 'Popular Services', 'home.popularServicesDesc' => 'Most ordered services by our customers',
            'home.startingFrom' => 'Starting from', 'home.per1000' => 'per 1000', 'home.orderNow' => 'Order Now',
            'home.allPlatforms' => 'Platforms', 'home.platformsWeSupport' => 'All Social Media Platforms',
            'home.platformsDesc' => 'Boost your presence on any platform',
            'home.readyToGrow' => 'Ready to Grow?', 'home.readyToGrowDesc' => 'Join thousands of satisfied customers and boost your social media today',
            'home.createAccount' => 'Create Account', 'home.quickSignIn' => 'Quick Sign In',
            'home.accessDashboard' => 'Access your dashboard instantly', 'home.password' => 'Password',
            'home.noAccount' => "Don't have an account?", 'home.activeUsers' => 'Active Users',
            'home.servicesCount' => 'Services', 'home.ordersCompleted' => 'Orders Completed',
            'home.support' => 'Support', 'home.threeSimpleSteps' => '3 Simple Steps',
            'home.getResultsTitle' => 'Get Results', 'home.getResultsDesc' => 'Place your order and watch your social media grow instantly!',
            'home.commonQuestions' => 'Common Questions', 'home.viewAllFaqs' => 'View All FAQs',
            'home.getStartedNow' => 'Get Started Now',
            'home.faq1Question' => 'How fast is the delivery?',
            'home.faq1Answer' => 'Most orders start within 0-15 minutes and complete within a few hours depending on the quantity.',
            'home.faq2Question' => 'Is it safe to use?',
            'home.faq2Answer' => 'Yes! We use safe methods and never ask for your password. Your account security is our priority.',
            'home.faq3Question' => 'What payment methods do you accept?',
            'home.faq3Answer' => 'We accept credit cards, PayPal, cryptocurrency, and various local payment methods.',
            'home.faq4Question' => 'Do you offer refunds?',
            'home.faq4Answer' => 'Yes, we offer full refunds if we fail to deliver your order. Contact our 24/7 support for assistance.',

            // Services Page
            'servicesPage.title' => 'All Services', 'servicesPage.subtitle' => 'Browse our complete list of SMM services',
            'servicesPage.searchPlaceholder' => 'Search services...', 'servicesPage.allCategories' => 'All Categories',
            'servicesPage.min' => 'Min', 'servicesPage.max' => 'Max', 'servicesPage.rate' => 'Rate',
            'servicesPage.per1000' => '/1K', 'servicesPage.orderNow' => 'Order Now',
            'servicesPage.noServices' => 'No services found', 'servicesPage.tryDifferent' => 'Try adjusting your filters or search terms',
            'servicesPage.allSmmServices' => 'All SMM Services', 'servicesPage.discoverServices' => 'Discover our complete range of social media marketing services',
            'servicesPage.servicesFound' => 'services found', 'servicesPage.totalServices' => 'Total Services',
            'servicesPage.platform' => 'Platform', 'servicesPage.category' => 'Category',
            'servicesPage.sortBy' => 'Sort By', 'servicesPage.order' => 'Order',
            'servicesPage.itemsPerPage' => 'Items per page', 'servicesPage.applyFilters' => 'Apply Filters',
            'servicesPage.clearFilters' => 'Clear Filters', 'servicesPage.featuredServices' => 'Featured Services',
            'servicesPage.viewDetails' => 'View Details', 'servicesPage.allServices' => 'All Services',
            'servicesPage.loadingServices' => 'Loading services...', 'servicesPage.service' => 'Service',
            'servicesPage.price' => 'Price', 'servicesPage.serviceId' => 'Service ID',
            'servicesPage.minOrder' => 'Min Order', 'servicesPage.maxOrder' => 'Max Order',
            'servicesPage.ascending' => 'Ascending', 'servicesPage.descending' => 'Descending',

            // About
            'about.title' => 'About Us', 'about.subtitle' => 'Learn more about our company and mission',
            'about.whoWeAre' => 'Who We Are', 'about.whoWeAreDesc' => 'We are a leading SMM panel providing high-quality social media marketing services at the most affordable prices.',
            'about.ourMission' => 'Our Mission', 'about.ourMissionDesc' => 'To provide the most reliable, affordable, and effective social media marketing services.',
            'about.ourVision' => 'Our Vision', 'about.ourVisionDesc' => 'To become the global leader in social media marketing services.',
            'about.whyChooseUs' => 'Why Choose Us',
            'about.exploreServices' => 'Explore Services', 'about.joinCustomers' => 'Join thousands of satisfied customers today',

            // Contact
            'contact.title' => 'Contact Us', 'contact.subtitle' => 'Get in touch with our team',
            'contact.getInTouch' => 'Get in Touch', 'contact.getInTouchDesc' => "Have questions? We're here to help!",
            'contact.name' => 'Your Name', 'contact.email' => 'Email Address', 'contact.subject' => 'Subject',
            'contact.message' => 'Your Message', 'contact.send' => 'Send Message', 'contact.sending' => 'Sending...',
            'contact.available247' => '24/7 Available', 'contact.within24' => 'Within 24 hours',

            // FAQ
            'faq.title' => 'Frequently Asked Questions', 'faq.subtitle' => 'Find answers to common questions',
            'faq.searchPlaceholder' => 'Search questions...', 'faq.contactSupport' => 'Contact Support',
            'faq.q1' => 'How fast is the delivery?', 'faq.a1' => 'Most orders start within 0-15 minutes.',
            'faq.q2' => 'Is it safe to use SMM services?', 'faq.a2' => 'Yes! We use safe delivery methods and never ask for your password.',
            'faq.q3' => 'What payment methods do you accept?', 'faq.a3' => 'We accept credit/debit cards, PayPal, cryptocurrency.',
            'faq.q4' => 'Do you offer refunds?', 'faq.a4' => 'Yes, we offer full refunds if we fail to deliver.',
            'faq.q5' => 'What is the minimum order?', 'faq.a5' => 'Minimum orders vary by service, typically starting from 10-100.',

            // How It Works
            'howItWorks.title' => 'How It Works', 'howItWorks.subtitle' => 'Get started in just 3 simple steps',
            'howItWorks.step1Title' => 'Create Account', 'howItWorks.step1Desc' => 'Sign up for free and add funds to your account',
            'howItWorks.step2Title' => 'Choose Service', 'howItWorks.step2Desc' => 'Select from our wide range of SMM services',
            'howItWorks.step3Title' => 'Watch Growth', 'howItWorks.step3Desc' => 'Sit back and watch your social media grow.',
            'howItWorks.signUpFree' => 'Sign Up Free', 'howItWorks.browseServices' => 'Browse Services',

            // Privacy
            'privacyPage.title' => 'Privacy Policy', 'privacyPage.subtitle' => 'How we handle your data',

            // Admin Nav
            'nav.dashboard' => 'Dashboard', 'nav.newOrder' => 'New Order', 'nav.orders' => 'Orders',
            'nav.services' => 'Services', 'nav.addBalance' => 'Add Balance', 'nav.transactions' => 'Transactions',
            'nav.support' => 'Support', 'nav.notifications' => 'Notifications', 'nav.profile' => 'Profile',
            'nav.logout' => 'Logout', 'nav.admin' => 'Admin', 'nav.users' => 'Users',
            'nav.roles' => 'Roles', 'nav.permissions' => 'Permissions', 'nav.paymentMethods' => 'Payment Methods',
            'nav.referrals' => 'Referrals', 'nav.points' => 'Points',
        ];
    }

    private function getArabicTranslations(): array
    {
        return [
            'common.home' => 'الرئيسية', 'common.save' => 'حفظ', 'common.cancel' => 'إلغاء',
            'common.delete' => 'حذف', 'common.edit' => 'تعديل', 'common.create' => 'إنشاء',
            'common.search' => 'بحث', 'common.filter' => 'تصفية', 'common.loading' => 'جاري التحميل...',
            'common.noData' => 'لا توجد بيانات', 'common.actions' => 'الإجراءات', 'common.status' => 'الحالة',
            'common.date' => 'التاريخ', 'common.amount' => 'المبلغ', 'common.total' => 'المجموع',
            'common.close' => 'إغلاق', 'common.confirm' => 'تأكيد', 'common.yes' => 'نعم', 'common.no' => 'لا',
            'common.all' => 'الكل', 'common.active' => 'نشط', 'common.inactive' => 'غير نشط',
            'common.success' => 'نجاح', 'common.error' => 'خطأ', 'common.warning' => 'تحذير',

            'publicNav.home' => 'الرئيسية', 'publicNav.services' => 'الخدمات', 'publicNav.smmPanel' => 'لوحة SMM',
            'publicNav.aboutUs' => 'من نحن', 'publicNav.contactUs' => 'اتصل بنا', 'publicNav.faq' => 'الأسئلة الشائعة',
            'publicNav.privacy' => 'الخصوصية', 'publicNav.howItWorks' => 'كيف يعمل',
            'publicNav.signIn' => 'تسجيل الدخول', 'publicNav.signUp' => 'إنشاء حساب', 'publicNav.language' => 'اللغة',
            'publicNav.lightMode' => 'الوضع الفاتح', 'publicNav.darkMode' => 'الوضع الداكن',

            'footer.quickLinks' => 'روابط سريعة', 'footer.legal' => 'قانوني', 'footer.contactUs' => 'اتصل بنا',
            'footer.supportAvailable' => 'الدعم متاح 24/7', 'footer.allRightsReserved' => 'جميع الحقوق محفوظة',
            'footer.madeWith' => 'صنع بـ', 'footer.forGrowth' => 'للنمو',
            'footer.description' => 'أرخص وأفضل لوحة SMM لإنستغرام وتيك توك ويوتيوب وفيسبوك والمزيد. توصيل فوري مع دعم 24/7.',
            'footer.bestServices' => 'أفضل خدمات SMM',

            'home.heroTitle' => 'نمّي حسابك على', 'home.heroTitleHighlight' => 'وسائل التواصل فوراً',
            'home.heroDescription' => 'أرخص لوحة SMM لإنستغرام وتيك توك ويوتيوب وفيسبوك للمتابعين والإعجابات والمشاهدات والمزيد. تبدأ من $0.01 فقط',
            'home.getStarted' => 'ابدأ مجاناً', 'home.viewServices' => 'عرض جميع الخدمات',
            'home.trustedBy' => 'موثوق به من', 'home.customers' => 'عميل حول العالم',
            'home.whyChooseUs' => 'لماذا تختارنا', 'home.whyChooseUsDesc' => 'نقدم أفضل خدمات SMM بجودة ودعم لا مثيل لهما',
            'home.instantDelivery' => 'توصيل فوري', 'home.instantDeliveryDesc' => 'تبدأ طلباتك في المعالجة فوراً بعد تأكيد الدفع',
            'home.securePayment' => 'دفع آمن', 'home.securePaymentDesc' => 'طرق دفع متعددة مع تشفير بمستوى البنوك',
            'home.support247' => 'دعم 24/7', 'home.support247Desc' => 'فريقنا المتفاني جاهز دائماً لمساعدتك في أي وقت',
            'home.bestPrices' => 'أرخص الأسعار', 'home.bestPricesDesc' => 'أسعار تنافسية مع أفضل جودة خدمات في السوق',
            'home.popularServices' => 'الخدمات الشائعة', 'home.startingFrom' => 'تبدأ من',
            'home.per1000' => 'لكل 1000', 'home.orderNow' => 'اطلب الآن',
            'home.allPlatforms' => 'المنصات', 'home.platformsWeSupport' => 'جميع منصات التواصل الاجتماعي',
            'home.readyToGrow' => 'مستعد للنمو؟', 'home.createAccount' => 'إنشاء حساب',
            'home.activeUsers' => 'مستخدم نشط', 'home.servicesCount' => 'خدمة', 'home.ordersCompleted' => 'طلب مكتمل',
            'home.commonQuestions' => 'الأسئلة الشائعة', 'home.getStartedNow' => 'ابدأ الآن',

            'servicesPage.title' => 'جميع الخدمات', 'servicesPage.subtitle' => 'تصفح قائمتنا الكاملة لخدمات SMM',
            'servicesPage.searchPlaceholder' => 'ابحث عن الخدمات...', 'servicesPage.allCategories' => 'جميع الفئات',
            'servicesPage.min' => 'الحد الأدنى', 'servicesPage.max' => 'الحد الأقصى', 'servicesPage.rate' => 'السعر',
            'servicesPage.orderNow' => 'اطلب الآن', 'servicesPage.noServices' => 'لم يتم العثور على خدمات',
            'servicesPage.allSmmServices' => 'جميع خدمات SMM', 'servicesPage.totalServices' => 'إجمالي الخدمات',
            'servicesPage.platform' => 'المنصة', 'servicesPage.category' => 'الفئة',
            'servicesPage.sortBy' => 'ترتيب حسب', 'servicesPage.ascending' => 'تصاعدي', 'servicesPage.descending' => 'تنازلي',

            'about.title' => 'من نحن', 'about.subtitle' => 'تعرف على شركتنا ومهمتنا',
            'contact.title' => 'اتصل بنا', 'contact.subtitle' => 'تواصل مع فريقنا',
            'faq.title' => 'الأسئلة الشائعة', 'faq.subtitle' => 'اعثر على إجابات للأسئلة الشائعة',
            'howItWorks.title' => 'كيف يعمل', 'howItWorks.subtitle' => 'ابدأ في 3 خطوات بسيطة',
            'howItWorks.step1Title' => 'إنشاء حساب', 'howItWorks.step2Title' => 'اختر الخدمة', 'howItWorks.step3Title' => 'شاهد النمو',
            'privacyPage.title' => 'سياسة الخصوصية', 'privacyPage.subtitle' => 'كيف نتعامل مع بياناتك',

            'nav.dashboard' => 'لوحة التحكم', 'nav.newOrder' => 'طلب جديد', 'nav.orders' => 'الطلبات',
            'nav.services' => 'الخدمات', 'nav.addBalance' => 'إضافة رصيد', 'nav.transactions' => 'المعاملات',
            'nav.support' => 'الدعم', 'nav.notifications' => 'الإشعارات', 'nav.profile' => 'الملف الشخصي',
            'nav.logout' => 'تسجيل الخروج', 'nav.admin' => 'الإدارة', 'nav.users' => 'المستخدمين',
        ];
    }
}
