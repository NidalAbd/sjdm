import { useHead } from '@vueuse/head'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

// SMM Panel related keywords for SEO optimization
const BASE_KEYWORDS = [
    'SMM panel',
    'social media marketing',
    'buy instagram followers',
    'buy tiktok followers',
    'buy youtube subscribers',
    'buy facebook likes',
    'buy twitter followers',
    'instagram likes',
    'tiktok views',
    'youtube views',
    'social media growth',
    'cheap SMM panel',
    'best SMM panel',
    'instant delivery SMM',
    'affordable SMM services',
    'social media promotion',
    'instagram growth',
    'tiktok growth',
    'youtube growth',
    'social media followers',
    'buy real followers',
    'SMM reseller panel',
    'bulk SMM services',
    'social media marketing panel'
]

const ARABIC_KEYWORDS = [
    'شراء متابعين انستقرام',
    'شراء متابعين تيك توك',
    'شراء مشتركين يوتيوب',
    'لايكات انستقرام',
    'مشاهدات يوتيوب',
    'زيادة متابعين',
    'تسويق سوشيال ميديا',
    'SMM بانل',
    'شراء متابعين حقيقيين',
    'زيادة لايكات',
    'تسويق الكتروني'
]

const SITE_NAME = 'SMM Panel'
const BASE_URL = 'https://smmjd.com'

export function useSeo(options = {}) {
    const { locale } = useI18n()

    const {
        title = '',
        description = '',
        keywords = [],
        image = '/images/logo.png',
        type = 'website',
        noIndex = false,
        canonical = '',
        structuredData = null
    } = options

    const isArabic = computed(() => locale.value === 'ar')

    const fullTitle = computed(() => {
        if (!title) return `${SITE_NAME} - Best SMM Panel for Social Media Growth`
        return `${title} | ${SITE_NAME}`
    })

    const fullKeywords = computed(() => {
        const baseKeywords = isArabic.value
            ? [...ARABIC_KEYWORDS, ...BASE_KEYWORDS]
            : [...BASE_KEYWORDS, ...ARABIC_KEYWORDS]
        return [...keywords, ...baseKeywords].join(', ')
    })

    const canonicalUrl = computed(() => {
        if (canonical) return `${BASE_URL}${canonical}`
        return BASE_URL
    })

    const headConfig = computed(() => {
        const config = {
            title: fullTitle.value,
            meta: [
                { name: 'description', content: description },
                { name: 'keywords', content: fullKeywords.value },
                { name: 'author', content: SITE_NAME },
                { name: 'robots', content: noIndex ? 'noindex, nofollow' : 'index, follow' },

                // Open Graph
                { property: 'og:title', content: fullTitle.value },
                { property: 'og:description', content: description },
                { property: 'og:image', content: `${BASE_URL}${image}` },
                { property: 'og:url', content: canonicalUrl.value },
                { property: 'og:type', content: type },
                { property: 'og:site_name', content: SITE_NAME },
                { property: 'og:locale', content: isArabic.value ? 'ar_SA' : 'en_US' },

                // Twitter Card
                { name: 'twitter:card', content: 'summary_large_image' },
                { name: 'twitter:title', content: fullTitle.value },
                { name: 'twitter:description', content: description },
                { name: 'twitter:image', content: `${BASE_URL}${image}` },

                // Mobile optimization
                { name: 'mobile-web-app-capable', content: 'yes' },
                { name: 'apple-mobile-web-app-capable', content: 'yes' },
                { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
                { name: 'apple-mobile-web-app-title', content: SITE_NAME },

                // Language
                { property: 'og:locale:alternate', content: isArabic.value ? 'en_US' : 'ar_SA' },
            ],
            link: [
                { rel: 'canonical', href: canonicalUrl.value }
            ]
        }

        // Add structured data if provided
        if (structuredData) {
            // Handle both single objects and arrays of structured data
            const dataArray = Array.isArray(structuredData) ? structuredData : [structuredData]
            config.script = dataArray.map(data => ({
                type: 'application/ld+json',
                children: JSON.stringify(data)
            }))
        }

        return config
    })

    useHead(headConfig)

    return {
        fullTitle,
        fullKeywords,
        canonicalUrl
    }
}

// Helper function to generate BreadcrumbList structured data
export function generateBreadcrumbs(items, isArabic = false) {
    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items.map((item, index) => ({
            '@type': 'ListItem',
            position: index + 1,
            name: item.name,
            ...(item.url ? { item: `${BASE_URL}${item.url}` } : {})
        }))
    }
}

// Pre-defined SEO configurations for each page
export const seoConfigs = {
    home: {
        title: 'Best SMM Panel - Buy Instagram, TikTok, YouTube Followers',
        description: 'The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Instant delivery, 24/7 support, starting at $0.01. Grow your social media instantly!',
        keywords: ['instant delivery', 'cheapest SMM', '24/7 support', 'social media growth service'],
        canonical: '/',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'WebApplication',
                name: 'SMM Panel',
                applicationCategory: 'SocialNetworkingApplication',
                operatingSystem: 'Web',
                offers: {
                    '@type': 'Offer',
                    price: '0.01',
                    priceCurrency: 'USD'
                },
                aggregateRating: {
                    '@type': 'AggregateRating',
                    ratingValue: '4.9',
                    reviewCount: '10000'
                }
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' }
                ]
            }
        ]
    },
    services: {
        title: 'All SMM Services - Instagram, TikTok, YouTube, Facebook',
        description: 'Browse our complete list of SMM services. Buy Instagram followers, TikTok views, YouTube subscribers, Facebook likes at the cheapest prices. Over 500+ services available.',
        keywords: ['SMM services', 'buy followers', 'buy likes', 'buy views', 'social media services', 'instagram services', 'tiktok services', 'youtube services'],
        canonical: '/all-services',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'ItemList',
                name: 'SMM Services',
                description: 'Complete list of social media marketing services',
                numberOfItems: '500+'
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'Services' }
                ]
            }
        ]
    },
    about: {
        title: 'About Us - Trusted SMM Panel Since 2020',
        description: 'Learn about our trusted SMM panel. We provide quality social media marketing services with instant delivery, secure payments, and 24/7 customer support. Over 10,000 satisfied customers.',
        keywords: ['trusted SMM panel', 'about us', 'social media experts', 'reliable SMM', 'SMM company'],
        canonical: '/about',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'Organization',
                name: 'SMM Panel',
                url: 'https://smmjd.com',
                foundingDate: '2020',
                description: 'Leading SMM panel for social media marketing services'
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'About Us' }
                ]
            }
        ]
    },
    contact: {
        title: 'Contact Us - 24/7 Customer Support',
        description: 'Get in touch with our support team. We offer 24/7 customer support for all your SMM needs. Email us at support@smmjd.com or open a support ticket.',
        keywords: ['contact SMM panel', 'customer support', '24/7 support', 'SMM help', 'support ticket'],
        canonical: '/contact-us',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'ContactPage',
                name: 'Contact SMM Panel',
                description: '24/7 customer support for SMM services'
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'Contact Us' }
                ]
            }
        ]
    },
    faq: {
        title: 'FAQ - Frequently Asked Questions',
        description: 'Find answers to common questions about our SMM panel. Learn about delivery times, payment methods, service quality, and how to place orders.',
        keywords: ['SMM FAQ', 'frequently asked questions', 'SMM help', 'how to buy followers', 'SMM guide'],
        canonical: '/faq',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'FAQPage',
                name: 'SMM Panel FAQ',
                description: 'Frequently asked questions about SMM services'
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'FAQ' }
                ]
            }
        ]
    },
    privacy: {
        title: 'Privacy Policy - Data Protection',
        description: 'Read our privacy policy to understand how we collect, use, and protect your personal information. Your privacy and data security are our priority.',
        keywords: ['privacy policy', 'data protection', 'SMM privacy', 'user data security'],
        canonical: '/privacy-policy',
        noIndex: false,
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'WebPage',
                name: 'Privacy Policy',
                description: 'Privacy policy for SMM Panel'
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'Privacy Policy' }
                ]
            }
        ]
    },
    howItWorks: {
        title: 'How It Works - Simple 3-Step Process',
        description: 'Learn how our SMM panel works in 3 easy steps. Create an account, add funds, and place your order. Instant delivery and automatic processing.',
        keywords: ['how to use SMM panel', 'SMM tutorial', 'buy followers guide', 'SMM process'],
        canonical: '/how-it-works',
        structuredData: [
            {
                '@context': 'https://schema.org',
                '@type': 'HowTo',
                name: 'How to Use SMM Panel',
                description: 'Simple 3-step guide to grow your social media',
                step: [
                    {
                        '@type': 'HowToStep',
                        name: 'Create Account',
                        text: 'Sign up for a free account in seconds'
                    },
                    {
                        '@type': 'HowToStep',
                        name: 'Add Funds',
                        text: 'Choose a payment method and add funds to your account'
                    },
                    {
                        '@type': 'HowToStep',
                        name: 'Place Order',
                        text: 'Select a service and place your order'
                    }
                ]
            },
            {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://smmjd.com' },
                    { '@type': 'ListItem', position: 2, name: 'How It Works' }
                ]
            }
        ]
    }
}

// Arabic SEO configurations
export const seoConfigsAr = {
    home: {
        title: 'أفضل بانل SMM - شراء متابعين انستقرام، تيك توك، يوتيوب',
        description: 'أرخص بانل SMM لشراء متابعين انستقرام، تيك توك، يوتيوب، فيسبوك. توصيل فوري، دعم على مدار الساعة، الأسعار تبدأ من $0.01!'
    },
    services: {
        title: 'جميع خدمات SMM - انستقرام، تيك توك، يوتيوب',
        description: 'تصفح قائمة خدماتنا الكاملة. شراء متابعين انستقرام، مشاهدات تيك توك، مشتركين يوتيوب بأرخص الأسعار.'
    },
    about: {
        title: 'من نحن - بانل SMM موثوق',
        description: 'تعرف على بانل SMM الموثوق لدينا. نقدم خدمات تسويق سوشيال ميديا عالية الجودة مع توصيل فوري ودعم على مدار الساعة.'
    },
    contact: {
        title: 'اتصل بنا - دعم على مدار الساعة',
        description: 'تواصل مع فريق الدعم لدينا. نقدم دعم على مدار الساعة لجميع احتياجاتك.'
    },
    faq: {
        title: 'الأسئلة الشائعة',
        description: 'اعثر على إجابات للأسئلة الشائعة حول بانل SMM الخاص بنا.'
    },
    privacy: {
        title: 'سياسة الخصوصية',
        description: 'اقرأ سياسة الخصوصية لفهم كيفية حماية بياناتك الشخصية.'
    },
    howItWorks: {
        title: 'كيف يعمل - 3 خطوات بسيطة',
        description: 'تعلم كيف يعمل بانل SMM في 3 خطوات سهلة. أنشئ حساب، أضف رصيد، وقدم طلبك.'
    }
}
