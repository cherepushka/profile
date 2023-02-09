<template>

     <div>
        <div 
            :class="{'triangle-up': direction === 'up', 'triangle-down': direction === 'down'}"
            class="triangle"
            :style="{
                'border-left-width': height,
                'border-right-width': height,
                'border-bottom-width': width,
                'border-color': 'transparent transparent ' + iconColor + ' transparent'
            }"
        >
        </div>
    </div>

</template>

<script>
export default {
    name: "Triangle",
    props: {
        width: {
            type: String,
            default: '8px',
        },
        height: {
            type: String,
            default: '6px',
        },
        iconColor: {
            type: String,
            default: 'rgb(55, 58, 60)'
        },
        direction: {
            type: String,
            default: 'up'
        }
    }
}
</script>

<style lang="scss" scoped>

.triangle{
    margin: 0;
    padding: 0;
    width: 0;
    height: 0;
    border-width: 0 6px 8px 6px;
    border-radius: 2px;
    border-color: transparent transparent rgb(55, 58, 60) transparent;
    border-style: inset;
}

.triangle-up{
    transform: rotate(0deg);
}

.triangle-down{
    transform: rotate(180deg);
}

</style>
