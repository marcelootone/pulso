export default function agenda(config) {
    return {
        eventos: config.eventos,
        formulario: {},
        podeEditar: true,
        enviando: false,
        confirmandoExclusao: false,
        mostrarPessoais: true,
        mostrarComuns: true,
        salvarUrl: config.salvarUrl,
        anoSelecionado: config.ano,
        mesSelecionado: config.mes,

        urlMes(mes) {
            return `${config.agendaUrl}?mes=${this.anoSelecionado}-${mes}`;
        },

        init() {
            this.formulario = this.dadosIniciais(config.hoje);

            if (config.antigo) {
                const evento = this.eventos[config.antigo.id];
                this.formulario = { ...this.formulario, ...evento, ...config.antigo };
                this.formulario.id = evento?.id ?? null;
                this.podeEditar = evento?.pode_editar ?? true;
            }
        },

        dadosIniciais(data) {
            return {
                id: null,
                titulo: '',
                descricao: '',
                local: '',
                data_inicio: data,
                data_fim: data,
                hora_inicio: '08:00',
                hora_fim: '09:00',
                dia_inteiro: false,
                compartilhado: false,
                proprio: true,
                autor: '',
            };
        },

        novo(data) {
            this.formulario = this.dadosIniciais(data);
            this.podeEditar = true;
            this.abrir();
        },

        editar(id) {
            this.formulario = { ...this.eventos[id] };
            this.podeEditar = this.formulario.pode_editar;
            this.abrir();
        },

        abrir() {
            this.enviando = false;
            this.confirmandoExclusao = false;
            this.$dispatch('open-modal', 'agenda-evento');
        },

        ajustarDataFim() {
            if (this.formulario.data_fim < this.formulario.data_inicio) {
                this.formulario.data_fim = this.formulario.data_inicio;
            }
        },
    };
}
