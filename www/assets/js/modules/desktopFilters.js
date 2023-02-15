export const desktopFiltersInit = () => {
  const toggleDesktopFilters = document.getElementById('toggleDesktopFilters')

  const desktopFilters = document.querySelector('.DesktopFilters')

  if (!desktopFilters) {
    return
  }

  toggleDesktopFilters.addEventListener('click', () => {
    desktopFilters.classList.toggle('-Opened')
    toggleDesktopFilters.classList.toggle('-Opened')
    localStorage.setItem(
      'desktopFiltersOpened',
      toggleDesktopFilters.classList.contains('-Opened') ? 'opened' : 'closed'
    )
  })

  const filtersState = localStorage.getItem('desktopFiltersOpened')
  if (filtersState == 'opened') {
    desktopFilters.classList.add('-Opened')
    toggleDesktopFilters.classList.add('-Opened')
  }

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
