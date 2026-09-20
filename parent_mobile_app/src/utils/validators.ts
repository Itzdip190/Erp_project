export const isValidEmail = (email: string): boolean => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email.trim());
};

export const isValidPhone = (phone: string): boolean => {
  const cleanPhone = phone.replace(/[^0-9]/g, '');
  return cleanPhone.length >= 10;
};

export const isValidSchoolCode = (code: string): boolean => {
  return code.trim().length >= 2;
};

export const isValidPassword = (password: string): boolean => {
  return password.length >= 4;
};
