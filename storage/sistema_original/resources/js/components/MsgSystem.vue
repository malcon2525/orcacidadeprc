<template>
    <transition name="msgsystem">
      <div v-if="mostrar" :class="['animatedDiv', tipo]">
        <span class="icon">
          <i v-if="tipo === 'sucesso'" class="bi bi-check-circle-fill"></i>
          <i v-else-if="tipo === 'erro'" class="bi bi-exclamation-circle-fill"></i>
          <i v-else class="bi bi-info-circle-fill"></i>
        </span>
        <span class="message">{{ mensagem }}</span>
        <button class="close-btn" @click="fechar">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </transition>
</template>

<script>
export default {
  props: {
    mostrar: {
      type: Boolean,
      required: true
    },
    tipo: {
      type: String,
      default: 'info',
      validator: (value) => ['sucesso', 'erro', 'info'].includes(value)
    },
    mensagem: {
      type: String,
      required: true
    },
    duracao: {
      type: Number,
      default: 5000
    }
  },
  watch: {
    mostrar(novoValor) {
      if (novoValor) {
        this.iniciarTimer();
      }
    }
  },
  methods: {
    fechar() {
      this.$emit('update:mostrar', false);
    },
    iniciarTimer() {
      if (this.duracao > 0) {
        setTimeout(() => {
          this.fechar();
        }, this.duracao);
      }
    }
  }
};
</script>

<style scoped>
.animatedDiv {
  width: 350px;
  min-height: 50px;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  position: fixed;
  top: 2%;
  left: calc(50% - 175px);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 9999;
  font-family: 'Inter', sans-serif;
}

.icon {
  margin-right: 12px;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
}

.message {
  flex: 1;
  text-align: left;
  font-size: 0.95rem;
  line-height: 1.4;
  font-weight: 500;
}

.close-btn {
  background: none;
  border: none;
  color: inherit;
  padding: 4px;
  cursor: pointer;
  opacity: 0.7;
  transition: opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  opacity: 1;
}

.sucesso {
  background-color: #28a745;
  color: #fff;
}

.erro {
  background-color: #dc3545;
  color: #fff;
}

.info {
  background-color: #7d8ab6;
  color: #fff;
}

.msgsystem-enter-active,
.msgsystem-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.msgsystem-enter-from,
.msgsystem-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.msgsystem-enter-to,
.msgsystem-leave-from {
  transform: translateY(0);
  opacity: 1;
}
</style>