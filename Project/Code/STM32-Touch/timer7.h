#if !defined(TIMER7_DEFINED)
	#define TIMER7_DEFINED
	
	void InitTimer7(void);
	void StartTimer7(void);
	void StopTimer7(void);

//	// Gebruik van de timer interrupt in de main.c, als volgt:
//	// Interrupt van Timer 7 opvangen.
//	void TIM7_IRQHandler(void)
//	{
//		if((TIM7->SR & TIM_SR_UIF) == TIM_SR_UIF)
//		{
//			// Interruptvlag resetten
//			TIM7->SR &= ~TIM_SR_UIF;
//			
//			...
//		}
//	}
#endif
