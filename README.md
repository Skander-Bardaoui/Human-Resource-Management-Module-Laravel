<p align="center">
  <img src="docs/banner.svg" width="100%" alt="HRM Pro banner" />
</p>

<p align="center">
  <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=22&pause=1200&color=10B981&center=true&vCenter=true&width=760&lines=Employees%2C+leave+%26+payroll;Laravel+%2B+Blade+%2B+Tailwind;Built+for+real+HR+workflows" alt="Typing tagline" />
</p>

<p align="center">
  <img src="https://img.shields.io/github/languages/top/Skander-Bardaoui/Human-Resource-Management-Module-Laravel?style=flat-square&color=10B981" alt="" />
  <img src="https://img.shields.io/github/stars/Skander-Bardaoui/Human-Resource-Management-Module-Laravel?style=flat-square&color=10B981" alt="" />
  <img src="https://img.shields.io/github/last-commit/Skander-Bardaoui/Human-Resource-Management-Module-Laravel?style=flat-square&color=34D399" alt="" />
  <img src="https://img.shields.io/badge/status-Active-10B981?style=flat-square" alt="" />
</p>

## Overview

A Laravel and Blade HR management module covering the employee lifecycle: a central directory, leave requests with hierarchical validation, attendance, payroll, recruitment, document management, departmental reports and role-based access. The workflows follow Tunisian labour-code and CNSS declaration requirements.

## Highlights

- Employee directory with position, contact and document records
- Leave requests with hierarchical approval workflow
- Attendance tracking and payroll preparation
- Recruitment pipeline and candidate follow-up
- Departmental reports and role-based access control
- Blade + Tailwind interface built for data-dense daily use

## Tech stack

<p align="center">
  <img src="https://skillicons.dev/icons?i=laravel,php,mysql,tailwind,alpinejs&theme=dark&perline=8" alt="Tech stack" />
</p>

## Screenshots

<p align="center">
  <a href="docs/screenshots/employees.webp"><img src="docs/screenshots/employees.webp" width="49%" alt="Employee directory" /></a>
  <a href="docs/screenshots/leave.webp"><img src="docs/screenshots/leave.webp" width="49%" alt="Leave management" /></a>
  <a href="docs/screenshots/reports.webp"><img src="docs/screenshots/reports.webp" width="49%" alt="Reports" /></a>
</p>

## Getting started

**Prerequisites:** PHP 8.2+, Composer, Node.js 20+ and MySQL.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# configure DB_* in .env, then:
php artisan migrate
npm run build
php artisan serve
```

## Documentation

- [Original project documentation](docs/README_ORIGINAL.md)

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:10B981,100:0A0A0C&height=110&section=footer" alt="footer" width="100%" />
</p>

<p align="center">
  Built by <a href="https://github.com/Skander-Bardaoui"><b>Skander Bardaoui</b></a> ·
  <a href="https://skander-bardaoui.netlify.app/">Portfolio</a> ·
  <a href="https://github.com/Skander-Bardaoui?tab=repositories">All repositories</a>
</p>
