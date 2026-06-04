export interface Job {
  id: number;
  title: string;
  description: string;
  experience: string;
  salary_min?: number;
  salary_max?: number;
  status: 'pending' | 'approved' | 'rejected';
  category_id: number;
  deadline: string;
  
  category?: {
    id: number;
    name: string;
  };
  employer?: {
    id: number;
    name: string;
    //TODO other data missed
  };
}