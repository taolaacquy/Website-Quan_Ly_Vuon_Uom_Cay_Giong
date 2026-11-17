<template>
  <main class="intro-page">
    <header class="site-header">
      <router-link to="/" class="brand" aria-label="Vườn Ươm Xanh - Trang chủ">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none">
            <path d="M31.9 55.4V29.8" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
            <path d="M31.8 37.2C21.1 36.5 14.1 29 14.5 17.4c10.6-.2 17.7 6.8 17.3 19.8Z" fill="currentColor" opacity=".9" />
            <path d="M32.3 31.3C34.1 19.4 41.7 13.2 52 12.6c.6 11.1-6.1 18.1-19.7 18.7Z" fill="currentColor" opacity=".68" />
            <path d="M19 55.5h27" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
          </svg>
        </span>
        <span>Vườn Ươm Xanh</span>
      </router-link>

      <nav class="header-actions" aria-label="Tài khoản">
        <router-link to="/login" class="login-link">Đăng nhập</router-link>
        <router-link to="/register" class="signup-link">Đăng ký <span aria-hidden="true">→</span></router-link>
      </nav>
    </header>

    <section class="hero">
      <div class="hero-copy">
        <p class="eyebrow"><span aria-hidden="true"></span> Gieo hôm nay · Xanh ngày mai</p>
        <h1>Mỗi khu vườn<br />đều bắt đầu từ<br /><em>một mầm xanh.</em></h1>
        <p class="hero-description">
          Tìm cây giống phù hợp, học cách chăm cây và cùng chúng mình vun đắp một không gian sống xanh hơn mỗi ngày.
        </p>
        <div class="hero-actions">
          <router-link to="/register" class="primary-button">Bắt đầu hành trình <span aria-hidden="true">→</span></router-link>
          <router-link to="/login" class="secondary-button">Mình đã có tài khoản</router-link>
        </div>
        <div class="hero-note">
          <span class="note-icon" aria-hidden="true">✳</span>
          <p><strong>Một lựa chọn nhỏ</strong><br />có thể tạo nên khác biệt lớn.</p>
        </div>
      </div>

      <div class="hero-visual" aria-label="Không gian vườn xanh với những mầm cây">
        <div class="hero-image"></div>
        <div class="image-label"><span>01</span><p>Nuôi dưỡng<br />điều xanh lành</p></div>
        <div class="plant-note">
          <span class="plant-note-icon" aria-hidden="true">✿</span>
          <div><strong>Trồng một mầm xanh</strong><small>Bắt đầu từ hôm nay</small></div>
          <span class="note-arrow" aria-hidden="true">↗</span>
        </div>
        <div class="decorative-ring" aria-hidden="true"></div>
      </div>
    </section>

    <section class="benefits" aria-labelledby="benefits-title">
      <div class="benefits-heading">
        <p class="eyebrow">Cùng nhau lớn lên</p>
        <h2 id="benefits-title">Thêm xanh cho từng ngày</h2>
      </div>
      <div class="benefit-list">
        <article class="benefit">
          <span class="benefit-number">01</span>
          <div><h3>Cây giống tuyển chọn</h3><p>Khám phá những mầm cây khỏe mạnh, sẵn sàng bén rễ trong khu vườn của bạn.</p></div>
          <span class="benefit-icon" aria-hidden="true">↗</span>
        </article>
        <article class="benefit">
          <span class="benefit-number">02</span>
          <div><h3>Chăm cây dễ dàng</h3><p>Tìm hiểu đặc tính và cách chăm sóc để cây luôn xanh tốt qua từng mùa.</p></div>
          <span class="benefit-icon" aria-hidden="true">↗</span>
        </article>
        <article class="benefit">
          <span class="benefit-number">03</span>
          <div><h3>Gieo trồng có ý nghĩa</h3><p>Mỗi lựa chọn xanh hôm nay góp phần tạo nên ngày mai trong lành hơn.</p></div>
          <span class="benefit-icon" aria-hidden="true">↗</span>
        </article>
      </div>
    </section>

    <footer class="site-footer">
      <router-link to="/" class="footer-brand">Vườn Ươm Xanh</router-link>
      <p>Gieo mầm hôm nay, xanh mãi ngày mai.</p>
      <router-link to="/register">Cùng bắt đầu <span aria-hidden="true">→</span></router-link>
    </footer>
  </main>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap');

.intro-page { min-height: 100vh; overflow: hidden; position: relative; isolation: isolate; color: #f8fbf5; background: #123523; font-family: 'DM Sans', sans-serif; }
.intro-page::before, .intro-page::after { position: absolute; z-index: -1; inset: 0; content: ''; pointer-events: none; }
.intro-page::before { background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=2200&q=85') center / cover no-repeat; }
.intro-page::after { background: linear-gradient(90deg, rgba(10, 40, 27, .92) 0%, rgba(13, 57, 35, .82) 52%, rgba(7, 27, 18, .78) 100%), linear-gradient(0deg, rgba(5, 25, 15, .55), rgba(5, 25, 15, .12)); }
.site-header { width: min(1240px, 100%); height: 88px; margin: auto; padding: 0 42px; display: flex; align-items: center; justify-content: space-between; }
.brand { display: inline-flex; align-items: center; gap: 10px; color: #f4faef; text-decoration: none; font-size: 1.05rem; font-weight: 700; }
.brand-mark { width: 40px; height: 40px; display: grid; place-items: center; color: #d8f5ba; border: 1px solid rgba(219, 249, 187, .45); border-radius: 50%; background: rgba(218, 249, 188, .12); backdrop-filter: blur(6px); }
.brand-mark svg { width: 29px; height: 29px; }
.header-actions { display: flex; align-items: center; gap: 27px; }
.login-link, .signup-link { color: #f0f7eb; text-decoration: none; font-size: .88rem; font-weight: 700; }
.signup-link { display: inline-flex; align-items: center; gap: 10px; padding: 11px 17px; color: #173b29; background: #d1f3ab; border-radius: 7px; }
.signup-link:hover, .primary-button:hover { background: #245c39; }
.hero { width: min(1240px, 100%); min-height: 575px; margin: auto; padding: 40px 42px 64px; display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
.hero-copy { position: relative; z-index: 1; padding: 16px 0 0 15px; }
.eyebrow { display: flex; align-items: center; gap: 9px; color: #ccefae; text-transform: uppercase; letter-spacing: .14em; font-size: .7rem; font-weight: 700; }
.hero-copy > .eyebrow span { width: 7px; height: 7px; border-radius: 50%; background: #d1f3ab; }
h1 { margin: 20px 0 19px; color: #fff; font: 700 clamp(3rem, 5.2vw, 4.65rem)/1.07 'Playfair Display', Georgia, serif; letter-spacing: -.05em; }
h1 em { color: #d1f3ab; }
.hero-description { max-width: 470px; color: rgba(249, 255, 247, .82); font-size: .98rem; line-height: 1.8; }
.hero-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 22px; margin-top: 29px; }
.primary-button { display: inline-flex; align-items: center; gap: 17px; min-height: 49px; padding: 0 19px; color: #173b29; background: #d1f3ab; border-radius: 7px; text-decoration: none; font-size: .86rem; font-weight: 700; transition: background .2s, transform .2s; }
.primary-button:hover { color: #fff; background: #245c39; transform: translateY(-2px); }
.secondary-button { color: #f0f7eb; text-decoration: none; font-size: .84rem; font-weight: 700; }
.login-link:hover, .secondary-button:hover, .site-footer a:hover { color: #d1f3ab; }
.hero-note { display: flex; align-items: center; gap: 12px; margin-top: 46px; }
.note-icon { width: 38px; height: 38px; display: grid; place-items: center; color: #d1f3ab; background: rgba(218, 249, 188, .14); border-radius: 50%; font-size: 1.15rem; }
.hero-note p { color: rgba(245, 255, 240, .7); font-size: .77rem; line-height: 1.5; }
.hero-note strong { color: #f5fff0; }
.hero-visual { position: relative; min-height: 472px; }
.hero-image { position: absolute; inset: 0 22px 15px 25px; background: linear-gradient(180deg, rgba(22, 46, 28, .02), rgba(20, 46, 29, .12)), url('https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=85') center 53% / cover; border-radius: 46% 46% 8px 8px; }
.hero-visual::before { position: absolute; content: ''; z-index: 1; inset: 0 22px 15px 25px; border: 1px solid rgba(255,255,255,.55); border-radius: 46% 46% 8px 8px; pointer-events: none; }
.image-label { position: absolute; z-index: 2; left: 47px; bottom: 47px; display: flex; align-items: flex-start; gap: 12px; color: #fff; }
.image-label span { padding-top: 4px; color: #d7edbd; font-size: .72rem; font-weight: 700; }
.image-label p { font: 600 1.15rem/1.35 'Playfair Display', Georgia, serif; }
.plant-note { position: absolute; z-index: 3; right: 0; bottom: 65px; display: flex; align-items: center; gap: 11px; min-width: 230px; padding: 15px 16px; color: #35523d; background: rgba(255,255,250,.96); border: 1px solid rgba(225,231,218,.8); border-radius: 9px; box-shadow: 0 12px 30px rgba(2, 23, 12, .3); }
.plant-note-icon { width: 34px; height: 34px; display: grid; place-items: center; color: #769658; background: #edf2e5; border-radius: 50%; font-size: 1.1rem; }
.plant-note strong, .plant-note small { display: block; }
.plant-note strong { font-size: .72rem; }
.plant-note small { margin-top: 3px; color: #8a968a; font-size: .65rem; }
.note-arrow { margin-left: auto; color: #83a46a; font-size: 1rem; }
.decorative-ring { position: absolute; z-index: -1; top: 20px; right: -40px; width: 130px; height: 130px; border: 1px solid rgba(223, 239, 216, .22); border-radius: 50%; }
.benefits { width: min(1156px, calc(100% - 84px)); margin: 13px auto 0; padding: 31px 0 55px; border-top: 1px solid rgba(227, 238, 222, .22); display: grid; grid-template-columns: .8fr 2fr; gap: 42px; }
.benefits-heading { padding-top: 9px; }
.benefits-heading .eyebrow { margin-bottom: 10px; }
h2 { color: #f5fff0; font: 600 1.65rem/1.25 'Playfair Display', Georgia, serif; }
.benefit-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: 21px; }
.benefit { position: relative; display: flex; gap: 11px; padding: 8px 12px 8px 0; }
.benefit-number { color: #d1f3ab; font-size: .67rem; font-weight: 700; }
.benefit h3 { margin: 0 20px 7px 0; color: #f5fff0; font-size: .79rem; font-weight: 700; }
.benefit p { color: rgba(245, 255, 240, .7); font-size: .72rem; line-height: 1.65; }
.benefit-icon { position: absolute; top: 5px; right: 0; color: #d1f3ab; }
.site-footer { width: min(1156px, calc(100% - 84px)); min-height: 70px; margin: auto; border-top: 1px solid rgba(227, 238, 222, .22); display: flex; align-items: center; justify-content: space-between; color: rgba(245, 255, 240, .7); font-size: .72rem; }
.site-footer a { color: #e6f4dc; text-decoration: none; font-weight: 700; }
.site-footer .footer-brand { font-size: .8rem; }
.site-footer a:last-child { display: inline-flex; gap: 8px; }
@media (max-width: 900px) {
  .site-header { padding: 0 26px; }
  .hero { min-height: auto; padding: 30px 26px 52px; gap: 28px; }
  .hero-copy { padding-left: 0; }
  .hero-visual { min-height: 400px; }
  .benefits { width: calc(100% - 52px); grid-template-columns: 1fr; gap: 22px; }
  .site-footer { width: calc(100% - 52px); }
}
@media (max-width: 640px) {
  .site-header { height: 72px; padding: 0 18px; }
  .brand { gap: 8px; font-size: .92rem; }
  .brand-mark { width: 35px; height: 35px; }
  .brand-mark svg { width: 25px; height: 25px; }
  .header-actions { gap: 13px; }
  .login-link { font-size: .78rem; }
  .signup-link { gap: 6px; padding: 9px 11px; font-size: .77rem; }
  .hero { padding: 26px 20px 40px; grid-template-columns: 1fr; gap: 31px; }
  h1 { margin: 15px 0; font-size: clamp(2.8rem, 12vw, 4rem); }
  .hero-description { max-width: 520px; font-size: .9rem; }
  .hero-actions { gap: 15px; margin-top: 22px; }
  .primary-button { min-height: 46px; padding: 0 14px; font-size: .78rem; }
  .secondary-button { font-size: .76rem; }
  .hero-note { margin-top: 25px; }
  .hero-visual { min-height: 360px; }
  .hero-image, .hero-visual::before { inset: 0 12px 12px 12px; }
  .image-label { left: 30px; bottom: 36px; }
  .plant-note { right: 0; bottom: 40px; min-width: 205px; padding: 12px; }
  .decorative-ring { right: -15px; }
  .benefits { width: calc(100% - 40px); padding: 27px 0 35px; }
  .benefit-list { grid-template-columns: 1fr; gap: 10px; }
  .benefit { padding: 11px 20px 11px 0; border-bottom: 1px solid rgba(227, 238, 222, .18); }
  .benefit h3 { font-size: .82rem; }
  .benefit p { max-width: 420px; }
  .site-footer { width: calc(100% - 40px); min-height: 80px; gap: 10px; flex-wrap: wrap; padding: 15px 0; }
  .site-footer p { order: 1; width: 100%; }
}
</style>
