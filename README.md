# Online Grocery Delivery Platform

Full-stack web application simulating a Blinkit-style online grocery delivery system with role-based access control, multi-store inventory, and end-to-end order workflows.

## Features

- User & Admin Roles: Secure login/registration with session-based authentication. Admins manage all data; customers browse, cart, and order.
- Database-Driven: MySQL backend with 15+ normalized entities (users, products, orders, payments, deliveries, vendors, stores, inventory, carts, coupons).
- Order Lifecycle: Cart → checkout (taxes, delivery fees, coupons) → payment → assignment to nearest dark store → rider tracking → delivery status.
- Inventory Management: Real-time stock tracking per dark store; automatic hiding of out-of-stock items.
- Dashboards: Admin CRUD for all modules; customer views for catalog, orders, and history.

## Tech Stack

- Backend: PHP, MySQL (PDO connectivity)
- Frontend: HTML/CSS
- Key Libraries: Session management, form validation

## Database Schema Overview

Designed for referential integrity and scalability:
- Entities: Users, Addresses, Vendors, Stores, Categories, Products, Inventory, Carts, Orders, Payments, Deliveries, Coupons, Reviews
- Relationships: Foreign keys enforce order-to-product, store-to-inventory, user-to-address
- ER diagram and full schema in `schema.sql`



