export const accordionInit = () => {
  // const accordions = document.querySelectorAll('.Accordion')
  // const toggle = (e, item) => {
  //   item.classList.toggle('-Opened')
  // }

  // accordions.forEach((accordion) => {
  //   const items = accordion.querySelectorAll('.Accordion__Item')
  //   items.forEach((item) => {
  //     item.removeEventListener('click', itemClick)
  //     item.addEventListener('click', itemClick)
  //   })
  // })

  // function itemClick(e) {
  //   console.log('click')
  //   toggle(e, e.currentTarget)
  // }

  document.addEventListener('click', (e) => {
    //console.log({ cur: e.currentTarget, tar: e.target, rel: e.relatedTarget })

    let heading
    if ((heading = e.target.closest('.Accordion__Heading '))) {
      heading.parentElement.classList.toggle('-Opened')
    }
  })
}
