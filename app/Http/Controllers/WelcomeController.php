<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    /**
     * SEO meta data per page per language
     */
    private function getSeoData(string $page, string $locale = 'en'): array
    {
        $baseUrl = config('app.url', 'https://smmjd.com');
        $supportedLangs = array_keys(config('app.available_locales'));

        $seo = [
            'home' => [
                'en' => [
                    'title' => 'Best SMM Panel - Buy Instagram, TikTok, YouTube Followers | Cheapest Prices',
                    'description' => 'The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Instant delivery, 24/7 support, starting at $0.01.',
                    'keywords' => 'SMM panel, social media marketing, buy instagram followers, buy tiktok followers, buy youtube subscribers, cheap SMM panel, best SMM panel',
                ],
                'ar' => [
                    'title' => 'أفضل لوحة SMM - شراء متابعين انستقرام، تيك توك، يوتيوب | أرخص الأسعار',
                    'description' => 'أرخص لوحة SMM لشراء متابعين انستقرام وتيك توك ويوتيوب وفيسبوك. توصيل فوري، دعم 24/7، تبدأ من $0.01.',
                    'keywords' => 'لوحة SMM, شراء متابعين, متابعين انستقرام, متابعين تيك توك, مشتركين يوتيوب',
                ],
                'es' => [
                    'title' => 'Mejor Panel SMM - Comprar Seguidores Instagram, TikTok, YouTube | Precios Baratos',
                    'description' => 'El panel SMM más barato para seguidores de Instagram, TikTok, YouTube, Facebook. Entrega instantánea, soporte 24/7, desde $0.01.',
                    'keywords' => 'panel SMM, marketing redes sociales, comprar seguidores instagram, comprar seguidores tiktok',
                ],
                'fr' => [
                    'title' => 'Meilleur Panel SMM - Acheter Followers Instagram, TikTok, YouTube | Prix les Plus Bas',
                    'description' => 'Le panel SMM le moins cher pour les followers Instagram, TikTok, YouTube, Facebook. Livraison instantanée, support 24/7, à partir de 0,01$.',
                    'keywords' => 'panel SMM, marketing réseaux sociaux, acheter followers instagram, acheter followers tiktok',
                ],
                'de' => [
                    'title' => 'Bestes SMM Panel - Instagram, TikTok, YouTube Follower kaufen | Günstigste Preise',
                    'description' => 'Das günstigste SMM Panel für Instagram, TikTok, YouTube, Facebook Follower, Likes, Views. Sofortige Lieferung, 24/7 Support, ab $0.01.',
                    'keywords' => 'SMM Panel, Social Media Marketing, Instagram Follower kaufen, TikTok Follower kaufen',
                ],
                'pt' => [
                    'title' => 'Melhor Painel SMM - Comprar Seguidores Instagram, TikTok, YouTube | Preços Mais Baratos',
                    'description' => 'O painel SMM mais barato para seguidores Instagram, TikTok, YouTube, Facebook. Entrega instantânea, suporte 24/7, a partir de $0.01.',
                    'keywords' => 'painel SMM, marketing redes sociais, comprar seguidores instagram, comprar seguidores tiktok',
                ],
                'ru' => [
                    'title' => 'Лучшая SMM Панель - Купить Подписчиков Instagram, TikTok, YouTube | Дешевые Цены',
                    'description' => 'Самая дешевая SMM панель для подписчиков Instagram, TikTok, YouTube, Facebook. Мгновенная доставка, поддержка 24/7, от $0.01.',
                    'keywords' => 'SMM панель, маркетинг в соцсетях, купить подписчиков инстаграм, купить подписчиков тикток',
                ],
                'zh' => [
                    'title' => '最佳SMM面板 - 购买Instagram、TikTok、YouTube粉丝 | 最便宜价格',
                    'description' => '最便宜的SMM面板，提供Instagram、TikTok、YouTube、Facebook粉丝、点赞、观看服务。即时交付，24/7支持，起价$0.01。',
                    'keywords' => 'SMM面板, 社交媒体营销, 购买Instagram粉丝, 购买TikTok粉丝',
                ],
                'ja' => [
                    'title' => '最高のSMMパネル - Instagram、TikTok、YouTubeフォロワー購入 | 最安値',
                    'description' => 'Instagram、TikTok、YouTube、Facebookのフォロワー、いいね、再生回数を最安値で。即時配信、24時間サポート、$0.01から。',
                    'keywords' => 'SMMパネル, SNSマーケティング, Instagramフォロワー購入, TikTokフォロワー購入',
                ],
                'ko' => [
                    'title' => '최고의 SMM 패널 - Instagram, TikTok, YouTube 팔로워 구매 | 최저가',
                    'description' => 'Instagram, TikTok, YouTube, Facebook 팔로워, 좋아요, 조회수를 최저가로. 즉시 배송, 24/7 지원, $0.01부터.',
                    'keywords' => 'SMM 패널, 소셜미디어 마케팅, 인스타그램 팔로워 구매, 틱톡 팔로워 구매',
                ],
                'hi' => [
                    'title' => 'सर्वश्रेष्ठ SMM पैनल - Instagram, TikTok, YouTube फॉलोअर्स खरीदें | सबसे सस्ती कीमतें',
                    'description' => 'Instagram, TikTok, YouTube, Facebook फॉलोअर्स, लाइक्स, व्यूज के लिए सबसे सस्ता SMM पैनल। तत्काल डिलीवरी, 24/7 सपोर्ट, $0.01 से शुरू।',
                    'keywords' => 'SMM पैनल, सोशल मीडिया मार्केटिंग, इंस्टाग्राम फॉलोअर्स खरीदें',
                ],
                'tr' => [
                    'title' => 'En İyi SMM Panel - Instagram, TikTok, YouTube Takipçi Satın Al | En Ucuz Fiyatlar',
                    'description' => 'Instagram, TikTok, YouTube, Facebook takipçi, beğeni, izlenme için en ucuz SMM panel. Anında teslimat, 7/24 destek, $0.01\'den başlayan fiyatlar.',
                    'keywords' => 'SMM panel, sosyal medya pazarlama, instagram takipçi satın al, tiktok takipçi satın al',
                ],
                'it' => [
                    'title' => 'Miglior Pannello SMM - Comprare Follower Instagram, TikTok, YouTube | Prezzi Più Bassi',
                    'description' => 'Il pannello SMM più economico per follower Instagram, TikTok, YouTube, Facebook. Consegna istantanea, supporto 24/7, da $0.01.',
                    'keywords' => 'pannello SMM, marketing social media, comprare follower instagram, comprare follower tiktok',
                ],
                'pl' => [
                    'title' => 'Najlepszy Panel SMM - Kup Obserwujących Instagram, TikTok, YouTube | Najtańsze Ceny',
                    'description' => 'Najtańszy panel SMM dla obserwujących Instagram, TikTok, YouTube, Facebook. Natychmiastowa dostawa, wsparcie 24/7, od $0.01.',
                    'keywords' => 'panel SMM, marketing w mediach społecznościowych, kup obserwujących instagram',
                ],
                'nl' => [
                    'title' => 'Beste SMM Panel - Koop Instagram, TikTok, YouTube Volgers | Goedkoopste Prijzen',
                    'description' => 'Het goedkoopste SMM panel voor Instagram, TikTok, YouTube, Facebook volgers, likes, views. Directe levering, 24/7 ondersteuning, vanaf $0.01.',
                    'keywords' => 'SMM panel, social media marketing, instagram volgers kopen, tiktok volgers kopen',
                ],
                'vi' => [
                    'title' => 'Bảng SMM Tốt Nhất - Mua Người Theo Dõi Instagram, TikTok, YouTube | Giá Rẻ Nhất',
                    'description' => 'Bảng SMM rẻ nhất cho người theo dõi Instagram, TikTok, YouTube, Facebook. Giao hàng tức thì, hỗ trợ 24/7, từ $0.01.',
                    'keywords' => 'bảng SMM, tiếp thị mạng xã hội, mua người theo dõi instagram',
                ],
                'th' => [
                    'title' => 'แผง SMM ที่ดีที่สุด - ซื้อผู้ติดตาม Instagram, TikTok, YouTube | ราคาถูกที่สุด',
                    'description' => 'แผง SMM ที่ถูกที่สุดสำหรับผู้ติดตาม Instagram, TikTok, YouTube, Facebook จัดส่งทันที สนับสนุน 24/7 เริ่มต้นที่ $0.01',
                    'keywords' => 'แผง SMM, การตลาดโซเชียลมีเดีย, ซื้อผู้ติดตาม instagram',
                ],
            ],
            'services' => [
                'en' => [
                    'title' => 'All SMM Services - Instagram, TikTok, YouTube, Facebook | SMM Panel',
                    'description' => 'Browse our complete list of SMM services. Buy followers, likes, views for Instagram, TikTok, YouTube, Facebook and more at the cheapest prices.',
                ],
                'ar' => [
                    'title' => 'جميع خدمات SMM - انستقرام، تيك توك، يوتيوب، فيسبوك | لوحة SMM',
                    'description' => 'تصفح قائمتنا الكاملة لخدمات SMM. اشترِ متابعين وإعجابات ومشاهدات لانستقرام وتيك توك ويوتيوب وفيسبوك بأرخص الأسعار.',
                ],
            ],
            'about' => [
                'en' => [
                    'title' => 'About Us - Best SMM Panel | SMMJD',
                    'description' => 'Learn about SMMJD - the most reliable and affordable SMM panel. Over 4 years of experience helping businesses grow on social media.',
                ],
                'ar' => [
                    'title' => 'من نحن - أفضل لوحة SMM | SMMJD',
                    'description' => 'تعرف على SMMJD - أفضل وأرخص لوحة SMM. أكثر من 4 سنوات خبرة في مساعدة الأعمال على النمو.',
                ],
            ],
            'faq' => [
                'en' => [
                    'title' => 'FAQ - Frequently Asked Questions | SMM Panel',
                    'description' => 'Find answers to common questions about our SMM panel services, delivery times, payment methods, refunds, and more.',
                ],
                'ar' => [
                    'title' => 'الأسئلة الشائعة | لوحة SMM',
                    'description' => 'اعثر على إجابات للأسئلة الشائعة حول خدمات لوحة SMM والتوصيل وطرق الدفع والاسترداد.',
                ],
            ],
            'contact' => [
                'en' => [
                    'title' => 'Contact Us - 24/7 Support | SMM Panel',
                    'description' => 'Get in touch with our support team. We offer 24/7 customer support for all your SMM panel needs.',
                ],
                'ar' => [
                    'title' => 'اتصل بنا - دعم 24/7 | لوحة SMM',
                    'description' => 'تواصل مع فريق الدعم. نقدم دعماً للعملاء 24/7 لجميع احتياجاتك.',
                ],
            ],
            'privacy' => [
                'en' => [
                    'title' => 'Privacy Policy | SMM Panel',
                    'description' => 'Read our privacy policy to understand how we handle your data securely.',
                ],
                'ar' => [
                    'title' => 'سياسة الخصوصية | لوحة SMM',
                    'description' => 'اقرأ سياسة الخصوصية لفهم كيفية تعاملنا مع بياناتك بأمان.',
                ],
            ],
            'how-it-works' => [
                'en' => [
                    'title' => 'How It Works - Get Started in 3 Steps | SMM Panel',
                    'description' => 'Learn how to use our SMM panel in 3 simple steps. Create account, add funds, place order and watch your social media grow.',
                ],
                'ar' => [
                    'title' => 'كيف يعمل - ابدأ في 3 خطوات | لوحة SMM',
                    'description' => 'تعلم كيفية استخدام لوحة SMM في 3 خطوات بسيطة. أنشئ حساباً، أضف رصيداً، قدم طلبك.',
                ],
            ],
        ];

        $pageData = $seo[$page] ?? $seo['home'];

        // Try exact locale match, then fallback to English
        $data = $pageData[$locale] ?? $pageData['en'];

        return array_merge($data, [
            'locale' => $locale,
            'supportedLangs' => $supportedLangs,
            'baseUrl' => $baseUrl,
            'page' => $page,
        ]);
    }

    private function renderSpa(string $page, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $seo = $this->getSeoData($page, $locale);
        return view('spa', compact('seo'));
    }

    public function index()
    {
        return $this->renderSpa('home');
    }

    public function indexOld()
    {
        $last24Hours = Carbon::now()->subDay();

        $usersCountLast24h = Cache::remember('users_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(User::class, $last24Hours) + rand(5000, 9500);
        });

        $transactionsCountLast24h = Cache::remember('transactions_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(Transaction::class, $last24Hours) + rand(12000, 25000);
        });

        $ordersCountLast24h = Cache::remember('orders_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(Order::class, $last24Hours) + rand(12000, 20000);
        });

        $totalUsersCount = $this->getTotalCountWithStartingPoint(User::class, 79778);
        $totalTransactionsCount = $this->getTotalCountWithStartingPoint(Transaction::class, 398890);
        $totalOrdersCount = $this->getTotalCountWithStartingPoint(Order::class, 254859);
        $completedOrdersCount = Order::where('status', 'completed')->count() + 254859 - 52581;

        return view('welcome', compact(
            'usersCountLast24h',
            'transactionsCountLast24h',
            'ordersCountLast24h',
            'totalUsersCount',
            'totalTransactionsCount',
            'totalOrdersCount',
            'completedOrdersCount'
        ));
    }

    private function getLast24HoursCount($model, $last24Hours)
    {
        return $model::where('created_at', '>=', $last24Hours)->count();
    }

    private function getTotalCountWithStartingPoint($model, $startingPoint)
    {
        return $model::count() + $startingPoint;
    }

    public function terms()
    {
        return $this->renderSpa('home');
    }

    public function faq()
    {
        return $this->renderSpa('faq');
    }

    public function about()
    {
        return $this->renderSpa('about');
    }

    public function howItWorks()
    {
        return $this->renderSpa('how-it-works');
    }

    public function support()
    {
        return view('widgets.support');
    }

    public function privacyPolicy()
    {
        return $this->renderSpa('privacy');
    }

    public function contact()
    {
        return $this->renderSpa('contact');
    }

    public function spa(Request $request, $locale = null)
    {
        // Determine which page this is for SEO
        $path = $request->path();
        $page = 'home';

        if (str_contains($path, 'all-services')) {
            $page = 'services';
        } elseif (str_contains($path, 'service/')) {
            $page = 'services';
        }

        return $this->renderSpa($page, $locale);
    }

    /**
     * Service detail page with dynamic SEO
     */
    public function serviceDetail($serviceId, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $service = Service::where('service_id', $serviceId)->first();

        $seo = $this->getSeoData('services', $locale);

        if ($service) {
            $name = $service->getTranslatedName($locale);
            $category = $service->getTranslatedCategory($locale);
            $seo['title'] = $name . ' - ' . $category . ' | SMM Panel';
            $seo['description'] = "Buy {$name} starting at \${$service->rate}/1K. Min: {$service->min}, Max: {$service->max}. Instant delivery, 24/7 support.";
        }

        return view('spa', compact('seo'));
    }
}
