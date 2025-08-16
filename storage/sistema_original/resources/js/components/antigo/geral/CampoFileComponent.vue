<template>
    <div>
        <div v-if="arquivo" class="mb-2">
            
            <div class="mt-1 ms-3 mb-3">
                <ul><li>{{ mostraNomeArquivo(arquivo) }}  
                    <span class="ico_del_arq ms-2" @click="apagarArquivo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg>
                    </span>
                </li></ul>
            </div>
        </div>

        <div class="mb-3" v-if="!arquivo">
           
            <input 
                type="file" 
                class="form-control" 
                :id="inputId" 
                @change="carregarArquivo">
            <div v-if="error" class="text-danger">{{ error }}</div> <!-- Exibe mensagens de erro -->
        </div>
    </div>
</template>

<script>
export default {
    props: {
        arquivo: {
            type: String,
            default: ''
        },
        inputId: {
            type: String,
            default: 'arquivo1'
        },
        
        maxSize: {
            type: Number,
            default: 10240 // Tamanho máximo em KB
        },
        allowedTypes: {
            type: Array,
            default: () => [
                'image/jpeg', 
                'image/png', 
                'application/pdf', 
                'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ] // Tipos permitidos
        }
    },
    data() {
        return {
            error: ''
        };
    },
    computed: {
        formatosPermitidos() {
            // Mapeia os tipos MIME para formatos amigáveis
            return this.allowedTypes.map(type => {
                switch (type) {
                    case 'image/jpeg': 
                        return 'jpeg';
                    case 'image/png': 
                        return 'png';
                    case 'application/pdf': 
                        return 'pdf';
                    case 'application/msword': 
                        return 'doc';
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 
                        return 'docx';
                    case 'application/vnd.ms-excel': 
                        return 'xls';
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 
                        return 'xlsx';
                    default: return type.split('/').pop(); // Pega o sufixo do tipo MIME como fallback
                }
            }).join(', ');
        }
    },
    methods: {
        carregarArquivo(event) {
            const file = event.target.files[0];
            if (file) {
                if (!this.allowedTypes.includes(file.type)) {
                    this.error = `Tipo de arquivo não permitido. Formatos permitidos: ${this.formatosPermitidos}.`;
                    this.limparCampoArquivo(event);
                } else if (file.size / 1024 > this.maxSize) {
                    this.error = `O arquivo excede o tamanho máximo permitido de ${this.maxSize} KB.`;
                    this.limparCampoArquivo(event);
                } else {
                    this.error = '';
                    this.$emit('carregar', file);
                }
            }
        },
        limparCampoArquivo(event) {
            event.target.value = ''; // Limpa o valor do campo de arquivo
        },
        apagarArquivo() {
            this.$emit('apagar');
        },
        mostraNomeArquivo(arquivo) {
            return arquivo.split('/').pop();
        }
    },
    mounted() {
        this.error = ''
    }
}
</script>

<style scoped>
.ico_del_arq {
    color: rgb(58, 58, 58);
}

.ico_del_arq:hover {
    cursor: pointer;
    color: red;
}
</style>