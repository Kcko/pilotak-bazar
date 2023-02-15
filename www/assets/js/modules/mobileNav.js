export const mobileNavInit = () => {
  // mobile nav
  const mainNavMobile = document.querySelector('.MainNavMobile')
  const openMobileNav = document.querySelector('[data-open-mobile-nav]')
  const closeMobileNav = document.querySelector('[data-close-mobile-nav]')

  openMobileNav.addEventListener('click', () => mainNavMobile.classList.add('-Opened'))
  closeMobileNav.addEventListener('click', () => mainNavMobile.classList.remove('-Opened'))
}
