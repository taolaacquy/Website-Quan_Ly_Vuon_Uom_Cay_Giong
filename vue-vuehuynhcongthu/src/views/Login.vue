<template>
  <main class="login-page">
    <div class="background" aria-hidden="true"></div>
    <div class="background-shade" aria-hidden="true"></div>

    <section class="login-layout">
      <div class="brand-panel">
        <router-link to="/login" class="brand" aria-label="Vườn Ươm Xanh">
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

        <div class="brand-copy">
          <p class="eyebrow">Chào mừng bạn trở lại</p>
          <h1>Một hạt giống,<br /><em>triệu nền xanh!</em></h1>
          <p class="intro">Mỗi ngày cùng nhau chăm chút cho những lựa chọn xanh và một tương lai trong lành hơn.</p>
        </div>

        <p class="quote">“Mầm xanh lớn lên từ những điều nhỏ bé.”</p>
      </div>

      <div class="form-column">
        <div class="login-card">
          <div class="card-heading">
            <span class="step">Vườn Ươm Xanh</span>
            <h2>Đăng nhập</h2>
            <p>Tiếp tục hành trình gieo những điều tốt đẹp.</p>
          </div>

          <form @submit.prevent="handleLogin">
            <label for="email">Email</label>
            <input id="email" v-model="email" type="email" placeholder="name@email.com" autocomplete="email" required />
            <label for="password">Mật khẩu</label>
            <input id="password" v-model="password" type="password" placeholder="Nhập mật khẩu" autocomplete="current-password" required />
            <button type="submit">Đăng nhập/Login <span aria-hidden="true">→</span></button>
          </form>

          <p class="register-link">Chưa có tài khoản? <router-link to="/register">Đăng ký ngay</router-link></p>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../services/api'

const email = ref('')
const password = ref('')
const router = useRouter()

const handleLogin = async () => {
  try {
    const result = await api<{ user: { id: number; fullname: string; email: string; role: string } }>('auth/login', {
      method: 'POST', body: { email: email.value, password: password.value }
    })
    if (!result.user) throw new Error('API đăng nhập không trả về tài khoản. Kiểm tra VITE_API_BASE_URL và PHP API.')
    localStorage.setItem('currentUser', JSON.stringify(result.user))
    router.push(result.user.role === 'admin' ? '/admin' : '/home')
  } catch (error) {
    alert(error instanceof Error ? error.message : 'Không thể đăng nhập.')
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap');
.login-page { min-height: 100vh; position: relative; overflow: hidden; color: #f8fbf5; font-family: 'DM Sans', sans-serif; }
.background { position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=2200&q=85') center / cover no-repeat; transform: scale(1.02); }
.background-shade { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(10, 40, 27, .90) 0%, rgba(13, 57, 35, .70) 45%, rgba(7, 27, 18, .50) 100%), linear-gradient(0deg, rgba(5, 25, 15, .38), transparent 55%); }
.login-layout { position: relative; z-index: 1; min-height: 100vh; width: min(1180px, 100%); margin: auto; padding: 38px 48px; display: grid; grid-template-columns: minmax(0, 1fr) 430px; gap: 70px; align-items: center; }
.brand-panel { align-self: stretch; display: flex; flex-direction: column; padding: 4px 0; }
.brand { display: inline-flex; align-items: center; gap: 10px; width: fit-content; color: inherit; text-decoration: none; font-weight: 700; letter-spacing: .01em; font-size: 1.08rem; }
.brand-mark { display: grid; place-items: center; width: 42px; height: 42px; color: #d8f5ba; border: 1px solid rgba(219, 249, 187, .45); border-radius: 50%; background: rgba(218, 249, 188, .12); backdrop-filter: blur(6px); }
.brand-mark svg { width: 30px; height: 30px; }
.brand-copy { margin: auto 0; max-width: 570px; }
.eyebrow, .step { color: #ccefae; text-transform: uppercase; letter-spacing: .13em; font-size: .72rem; font-weight: 700; }
h1 { margin: 12px 0 18px; color: #fff; font: 700 clamp(2.7rem, 5vw, 4.75rem)/1.05 'Playfair Display', Georgia, serif; letter-spacing: -.045em; }
h1 em { color: #d1f3ab; }
.intro { max-width: 480px; font-size: 1.05rem; line-height: 1.75; color: rgba(249, 255, 247, .82); }
.quote { color: rgba(245, 255, 240, .7); font: italic 1rem 'Playfair Display', Georgia, serif; }
.form-column { display: flex; justify-content: flex-end; }
.login-card { width: 100%; padding: 38px; color: #173526; background: rgba(255, 255, 252, .95); border: 1px solid rgba(255, 255, 255, .8); border-radius: 22px; box-shadow: 0 24px 70px rgba(2, 23, 12, .30); }
.card-heading { margin-bottom: 26px; }
.card-heading .step { color: #57844c; }
h2 { margin: 5px 0; color: #173b29; font: 700 2rem/1.2 'Playfair Display', Georgia, serif; letter-spacing: -.025em; }
.card-heading p { font-size: .9rem; line-height: 1.5; color: #6a7b70; }
form { display: flex; flex-direction: column; gap: 7px; }
label { margin-top: 8px; color: #355342; font-size: .82rem; font-weight: 700; }
input { width: 100%; height: 48px; padding: 0 14px; font: inherit; color: #193a29; background: #f6f8f3; border: 1px solid #dce5da; border-radius: 9px; outline: none; transition: border-color .2s, box-shadow .2s, background .2s; }
input::placeholder { color: #98a69d; }
input:focus { border-color: #669b59; background: #fff; box-shadow: 0 0 0 3px rgba(103, 155, 89, .14); }
button { display: flex; align-items: center; justify-content: center; gap: 10px; height: 50px; margin-top: 16px; border: 0; border-radius: 9px; color: #fff; background: #2e6f45; box-shadow: 0 8px 16px rgba(34, 94, 54, .23); font: 700 .95rem inherit; cursor: pointer; transition: transform .2s, background .2s, box-shadow .2s; }
button span { font-size: 1.22rem; transition: transform .2s; }
button:hover { background: #245d39; box-shadow: 0 10px 20px rgba(34, 94, 54, .30); transform: translateY(-1px); }
button:hover span { transform: translateX(3px); }
.register-link { margin: 22px 0 0; text-align: center; color: #718177; font-size: .88rem; }
.register-link a { color: #2c7045; font-weight: 700; text-decoration: none; }
.register-link a:hover { text-decoration: underline; }
@media (max-width: 800px) { .login-layout { grid-template-columns: 1fr; gap: 30px; padding: 28px 22px; } .brand-panel { min-height: 210px; } .quote { display: none; } .form-column { justify-content: center; } .login-card { max-width: 460px; } h1 { font-size: clamp(2.4rem, 10vw, 3.5rem); } }
@media (max-width: 480px) { .login-layout { padding: 18px; } .brand-panel { min-height: 190px; } .login-card { padding: 28px 22px; border-radius: 18px; } }
</style>
