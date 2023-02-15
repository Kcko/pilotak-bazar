export const mobileFiltersInit = () => {
  const root = document.documentElement
  const showMobileFilters = document.getElementById('showMobileFilters')
  const closeMobileFilters = document.getElementById('closeMobileFilters')
  const mobileFilters = document.querySelector('.MobileFilters')
  const mobileSearch = document.querySelector('#MobileSearch')

  if (!showMobileFilters) {
    return
  }

  showMobileFilters.addEventListener('click', () => {
    mobileFilters.classList.add('-Opened')
    root.classList.add('Lock')
  })

  closeMobileFilters.addEventListener('click', () => {
    mobileFilters.classList.remove('-Opened')
    root.classList.remove('Lock')
  })

  mobileSearch.addEventListener('click', () => {
    mobileFilters.classList.remove('-Opened')
    root.classList.remove('Lock')
  })

  // filtry

  let filterGroups = document.querySelectorAll('.FilterCheckboxListGroup')

  filterGroups.forEach((group) => {
    let checkboxes = group.querySelectorAll('input')
    checkboxes.forEach((checkbox) => {
      checkbox.onclick = () => {
        // kdyz to neni prvni = vse, tak odznacim vse
        if (checkbox.value != 0) {
          checkboxes[0].checked = false
        } else {
          // pokud klikam na vse, a davam ho jako checked, tak ostatni pod nim odznacim
          if (checkbox.checked) {
            checkboxes.forEach((checkbox) => {
              if (checkbox.value != 0) {
                checkbox.checked = false
              }
            })
          }
          // nemuzu odnacit vse
          else {
            checkbox.checked = true
          }
        }
      }
    })
  })
}
