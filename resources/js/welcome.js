  // Navbar active state
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".navbar-links a");
  window.addEventListener("scroll", () => {
    let current = "";
    sections.forEach(sec => {
      const top = window.scrollY;
      const offset = sec.offsetTop - 150;
      const height = sec.offsetHeight;
      if (top >= offset && top < offset + height) {
        current = sec.getAttribute("id");
      }
    });
    navLinks.forEach(link => {
      link.classList.remove("active");
      if (link.getAttribute("href") === `#${current}`) link.classList.add("active");
    });
  });

  // About and Footer animation
  const aboutSection = document.querySelector(".about-section");
  const footer = document.querySelector("footer");
  window.addEventListener("scroll", () => {
    const triggerBottom = window.innerHeight * 0.85;
    if (aboutSection.getBoundingClientRect().top < triggerBottom) {
      aboutSection.classList.add("about-visible");
    }
    if (footer.getBoundingClientRect().top < triggerBottom) {
      footer.classList.add("visible");
    }
  });

  // ===============================
  // Hero background switching (auto + manual)
  // ===============================
  const heroBgs = document.querySelectorAll(".hero-bg");
  const leftBtn = document.querySelector(".hero-arrow.left");
  const rightBtn = document.querySelector(".hero-arrow.right");
  let currentIndex = 0;

  // Function to change background
  function changeBackground(next = true) {
    heroBgs[currentIndex].classList.remove("active");
    if (next) {
      currentIndex = (currentIndex + 1) % heroBgs.length;
    } else {
      currentIndex = (currentIndex - 1 + heroBgs.length) % heroBgs.length;
    }
    heroBgs[currentIndex].classList.add("active");
  }

  // Manual arrows
  rightBtn.addEventListener("click", () => changeBackground(true));
  leftBtn.addEventListener("click", () => changeBackground(false));

  // Automatic switching every 6 seconds
  setInterval(() => changeBackground(true), 6000);
  // ===============================
// Toggle "More Details" section
// ===============================
const toggleBtn = document.getElementById("toggle-details");
const details = document.getElementById("details-content");
let visible = false;

toggleBtn.addEventListener("click", () => {
  visible = !visible;
  details.classList.toggle("show");
  toggleBtn.innerHTML = visible
    ? '<i class="fa-solid fa-circle-xmark ms-2"></i> إخفاء التفاصيل'
    : '<i class="fa-solid fa-circle-info ms-2"></i> المزيد من التفاصيل';
});
