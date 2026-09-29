# vue-vuehuynhcongthu

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Recommended Browser Setup

- Chromium-based browsers (Chrome, Edge, Brave, etc.):
  - [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd)
  - [Turn on Custom Object Formatter in Chrome DevTools](http://bit.ly/object-formatters)
- Firefox:
  - [Vue.js devtools](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)
  - [Turn on Custom Object Formatter in Firefox DevTools](https://fxdx.dev/firefox-devtools-custom-object-formatters/)

## Type Support for `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) to make the TypeScript language service aware of `.vue` types.

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```
```mermaidSodoERD
erDiagram
    MySQLserver
    USERS {
        int id PK
        string full_name
        string email
        string password_hash
        string phone
        string address
        string role "admin | customer"
        datetime created_at
    }

    CATEGORIES {
        int id PK
        string name "Cây ăn trái, Cây hoa, Cây công trình..."
        string description
        datetime created_at
    }

    PRODUCTS {
        int id PK
        int category_id FK
        string name "Tên cây giống"
        string origin "Nguồn gốc / Xuất xứ"
        text care_instructions "Hướng dẫn kỹ thuật chăm sóc"
        decimal price
        int stock_quantity "Số lượng cây tồn trong vườn"
        string image_url
        datetime created_at
    }

    ORDERS {
        int id PK
        int user_id FK
        decimal total_amount
        string status "pending | processing | shipping | completed | cancelled"
        string shipping_address
        string payment_method
        datetime created_at
    }

    ORDER_ITEMS {
        int id PK
        int order_id FK
        int product_id FK
        int quantity
        decimal unit_price
    }

    CART_ITEMS {
        int id PK
        int user_id FK
        int product_id FK
        int quantity
        datetime created_at
    }

    INQUIRIES {
        int id PK
        int user_id FK "Có thể null nếu khách không đăng nhập"
        string guest_name
        string guest_phone
        string guest_email
        text message "Yêu cầu tư vấn kỹ thuật trồng"
        string status "pending | processed"
        datetime created_at
    }

    CATEGORIES ||--o{ PRODUCTS : "phân loại"
    USERS ||--o{ ORDERS : "đặt hàng"
    ORDERS ||--|{ ORDER_ITEMS : "bao gồm"
    PRODUCTS ||--o{ ORDER_ITEMS : "nằm trong"
    USERS ||--o{ CART_ITEMS : "quản lý"
    PRODUCTS ||--o{ CART_ITEMS : "được thêm vào"
    USERS ||--o{ INQUIRIES : "gửi"
  ```
