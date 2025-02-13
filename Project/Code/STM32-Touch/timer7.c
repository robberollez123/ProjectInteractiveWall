#include "stm32f091xc.h"
#include "timer7.h"

void InitTimer7(void) {
	RCC->APB1ENR |= RCC_APB1ENR_TIM7EN;   // Klok voor Timer7 inschakelen
	
	TIM7->PSC = 47999;  // Prescaler: 48 MHz / 48000 = 1 kHz (1 ms ticks)
	TIM7->ARR = 49;      // Auto-reload: 10 ms interrupt
	TIM7->DIER |= TIM_DIER_UIE;  // Interrupt inschakelen
	NVIC_SetPriority(TIM7_IRQn, 1);  // Prioriteit instellen
	NVIC_EnableIRQ(TIM7_IRQn);       // Interrupt inschakelen
}

void StartTimer7(void) {
	TIM7->CNT = 0;                  // Reset timer counter
	TIM7->SR &= ~TIM_SR_UIF;        // Reset update-vlag
	TIM7->CR1 |= TIM_CR1_CEN;       // Start timer
}

void StopTimer7(void)
{	
  TIM7->CR1 &= ~TIM_CR1_CEN;      // Stop timeR
}
