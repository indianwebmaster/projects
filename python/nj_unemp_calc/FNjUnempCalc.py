class FNjUnempCalc:
    def __init__(self):
        self.weekly_benefit_rate = None
        self.hours_per_week = None
        self.hourly_rate = None
        self.earnings = None
        self.tax_rate = 0.1

        self.gross_WBR_before_tax = 0
        self.net_WBR_after_tax = 0

    def calculate_fulltime_WBR(self):
        weeks_worked_last_year = 52     # Number of weeks worked between Oct-01 and Sep-30 of the previous year
        total_income_before_taxes = 19500   # Total gross income before taxes between Oct-01 and Sep-30 of the previous year
        num_dependents = 0  # Number of dependents you can claim between Oct-01 and Sep-30 of the previous year

        if weeks_worked_last_year < 20: return 0
        if total_income_before_taxes < 8500: return 0
        earnings_per_week = (total_income_before_taxes / weeks_worked_last_year)
        if earnings_per_week < 169: return 0

        highest_quarter_wages_earned = total_income_before_taxes/4
        weekly_benefit_rate = int(1.2 * highest_quarter_wages_earned / 26.0)
        return weekly_benefit_rate

    def setvars(self, weekly_benefit_rate=None,hours_per_week=None,hourly_rate=None,earnings=None,tax_rate=None):
        if weekly_benefit_rate is not None: self.weekly_benefit_rate = weekly_benefit_rate
        if hours_per_week is not None: self.hours_per_week = hours_per_week; self.earnings = None
        if hourly_rate is not None: self.hourly_rate = hourly_rate; self.earnings = None
        if earnings is not None: self.earnings = earnings
        if tax_rate is not None: self.tax_rate = tax_rate

        if self.earnings is None and self.hours_per_week is not None and self.hourly_rate is not None:
            self.earnings = self.hours_per_week * self.hourly_rate

    def getResults(self):
        return (self.gross_WBR_before_tax,self.net_WBR_after_tax)

    def calculate_parttime_WBR(self,weekly_benefit_rate=None,hours_per_week=None,hourly_rate=None,earnings=None,tax_rate=None):
        self.setvars(weekly_benefit_rate,hours_per_week,hourly_rate,earnings,tax_rate)
        # any amount earned exceeding 20% of the WBR is deducted from the WBR
        WBR_20percent = 0.2 * self.weekly_benefit_rate
        earnings_to_deduct = self.earnings - WBR_20percent
        self.gross_WBR_before_tax = self.weekly_benefit_rate - earnings_to_deduct
        self.net_WBR_after_tax = self.gross_WBR_before_tax * (1.0 - self.tax_rate)
        return self.getResults()

if __name__ == "__main__":
    njunempcalc = FNjUnempCalc()
    weekly_benefit_rate = 225; hours_per_week = 20; hourly_rate = 11; tax_rate = 0.1
    (grossWBR, netWBR) = njunempcalc.calculate_parttime_WBR(weekly_benefit_rate,hours_per_week,hourly_rate,None,tax_rate)
    print (F"Gross WBR = {grossWBR}, Net WBR = {netWBR}")

    weekly_benefit_rate = 225; hours_per_week = 20; hourly_rate = 12; tax_rate = 0.1
    (grossWBR, netWBR) = njunempcalc.calculate_parttime_WBR(weekly_benefit_rate,hours_per_week,hourly_rate,None,tax_rate)
    print (F"Gross WBR = {grossWBR}, Net WBR = {netWBR}")

    print(njunempcalc.calculate_fulltime_WBR())