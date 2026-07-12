<template>
    <footer class="pub-footer">
        <v-container>
            <div class="footer-grid">
                <div>
                    <div class="footer-brand-row">
                        <img src="/images/logo.png" alt="SMM Panel" width="32" height="32">
                        <span>SMM Panel</span>
                    </div>
                    <p class="footer-desc">{{ $t('footer.description') }}</p>
                </div>
                <div>
                    <div class="footer-heading">{{ $t('footer.quickLinks') }}</div>
                    <ul class="footer-links">
                        <li><router-link to="/">{{ $t('publicNav.home') }}</router-link></li>
                        <li><router-link to="/all-services">{{ $t('publicNav.services') }}</router-link></li>
                        <li><router-link to="/faq">{{ $t('publicNav.faq') }}</router-link></li>
                        <li><router-link to="/contact-us">{{ $t('publicNav.contactUs') }}</router-link></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-heading">{{ $t('footer.legal') }}</div>
                    <ul class="footer-links">
                        <li><router-link to="/privacy-policy">{{ $t('publicNav.privacy') }}</router-link></li>
                        <li><router-link to="/about">{{ $t('publicNav.aboutUs') }}</router-link></li>
                        <li><router-link to="/how-it-works">{{ $t('publicNav.howItWorks') }}</router-link></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-heading">{{ $t('footer.contactUs') }}</div>
                    <div class="footer-contact-row">
                        <v-icon size="14">mdi-email</v-icon> support@smmjd.com
                    </div>
                    <div class="footer-contact-row">
                        <v-icon size="14">mdi-clock</v-icon> {{ $t('footer.supportAvailable') }}
                    </div>
                </div>
            </div>
            <div class="footer-copy">&copy; {{ new Date().getFullYear() }} SMM Panel. {{ $t('footer.allRightsReserved') }}.</div>
        </v-container>
    </footer>
</template>
