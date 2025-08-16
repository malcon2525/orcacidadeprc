document.addEventListener('DOMContentLoaded', function() {
    // Script para navegação customizada das abas
    const tabLinks = document.querySelectorAll('.orcacidade-tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    tabLinks.forEach(btn => {
        btn.addEventListener('click', function() {
            tabLinks.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tab = this.getAttribute('data-tab');
            tabPanes.forEach(pane => {
                if (pane.id === tab) {
                    pane.classList.add('show', 'active');
                } else {
                    pane.classList.remove('show', 'active');
                }
            });
        });
    });

    // Ativar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Adicionar efeitos de hover nos cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    console.log('Página de andamento do projeto carregada com dados reais');

    // Carregar a aba Relatórios dinamicamente
    const relatoriosTab = document.getElementById('relatorios-tab');
    if (relatoriosTab) {
        relatoriosTab.addEventListener('shown.bs.tab', function () {
            const container = document.getElementById('relatorios-dinamico');
            if (!container.dataset.loaded) {
                fetch('/andamento-projeto/relatorios', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => resp.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.dataset.loaded = '1';
                    })
                    .catch(() => {
                        container.innerHTML = '<span class="text-danger">Erro ao carregar relatórios.</span>';
                    });
            }
        });
    }

    // Função para abrir relatório individual
    window.abrirRelatorio = function(basename, filename) {
        const modal = new bootstrap.Modal(document.getElementById('relatorioModal'));
        document.getElementById('relatorioModalLabel').innerText = filename;
        document.getElementById('relatorioConteudo').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Carregando...';
        fetch(`/andamento-projeto/relatorio/${basename}`)
            .then(resp => resp.json())
            .then(data => {
                document.getElementById('relatorioConteudo').innerHTML = data.html;
            })
            .catch(() => {
                document.getElementById('relatorioConteudo').innerHTML = '<span class="text-danger">Erro ao carregar relatório.</span>';
            });
        modal.show();
    }

    // Função para toggle de acordeões (fases-e-sprints)
    window.toggleAccordion = function(marcoId) {
        // Fechar todos os outros acordeões
        const allCollapses = document.querySelectorAll('.accordion-collapse');
        allCollapses.forEach(collapse => {
            if (collapse.id !== marcoId) {
                const bsCollapse = new bootstrap.Collapse(collapse, {toggle: false});
                bsCollapse.hide();
            }
        });
        
        // Abrir o acordeão clicado
        const targetCollapse = document.getElementById(marcoId);
        if (targetCollapse) {
            const bsCollapse = new bootstrap.Collapse(targetCollapse, {toggle: false});
            bsCollapse.show();
            
            // Scroll suave para o acordeão
            setTimeout(() => {
                targetCollapse.closest('.accordion-item').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 300);
        }
    }
}); 