

<script>
import { Chart } from 'chart.js';

export default {
    props: ['type', 'endpoint'],
    data() {
        return {
            chart: null,
            loading: false
        }
    },
    methods: {
        async fetchData(params = {}) {
            this.loading = true;
            const { data } = await axios.get(this.endpoint, { params });
            this.chart.data = data;
            this.chart.update();
            this.loading = false;
        }
    }
}
</script>
