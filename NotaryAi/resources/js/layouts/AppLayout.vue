<template>
  <div class="app-layout">
    <header>
      <h1 class="app-title">NotaryAi</h1>
      <nav>
        <ul class="nav-links">
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/calculator">Calculator</router-link></li>
          <!-- Add more navigation links as needed -->
        </ul>
      </nav>
    </header>
    <main>
      <slot></slot>
    </main>
    <footer>
      <p>&copy; 2023 NotaryAi. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

header {
  background-color: #f8f9fa;
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.app-title {
  margin: 0;
}

.nav-links {
  list-style: none;
  padding: 0;
}

.nav-links li {
  display: inline;
  margin-right: 1rem;
}

footer {
  background-color: #f8f9fa;
  padding: 1rem;
  text-align: center;
  margin-top: auto;
}
</style>